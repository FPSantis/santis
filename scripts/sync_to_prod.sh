#!/bin/bash

# ==============================================================================
# Script de Upload para Produção (FULL DEPLOY)
# 1. Atualiza Dados Padrão (JSON)
# 2. Gera Dump do Banco ("Default State")
# 3. Exporta Banco Atual ("Current State")
# 4. Envia para Hostinger (Uploads Mirror + Import DB)
# ==============================================================================

# 1. Carregar Configuração
CONFIG_FILE="$(dirname "$0")/deploy/deploy.config"

if [ ! -f "$CONFIG_FILE" ]; then
    echo "❌ Erro: Arquivo de configuração '$CONFIG_FILE' não encontrado."
    exit 1
fi

source "$CONFIG_FILE"

# Validação
if [[ -z "$SSH_HOST" || -z "$SSH_USER" || -z "$SSH_PATH" ]]; then
    echo "❌ Erro: Variáveis de configuração SSH obrigatórias não preenchidas."
    exit 1
fi

# Diretórios
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
CODE_DIR="$PROJECT_ROOT/code"
TEMP_DIR="$SCRIPT_DIR/deploy_temp"
DATE_NOW=$(date +%Y%m%d_%H%M%S)

echo "🚀 INICIANDO UPLOAD PARA PRODUÇÃO..."
echo "========================================================"
echo "⚠️  ATENÇÃO: ISSO IRÁ SOBRESCREVER O AMBIENTE DE PRODUÇÃO!"
echo "   - Banco de dados será substituído pelo local."
echo "   - Arquivos de Upload remotos que não existem localmente SERÃO APAGADOS."
echo "   Você tem certeza que deseja continuar?"
read -p "   (y/n)? " -n 1 -r
echo ""
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "❌ Abortado pelo usuário."
    exit 1
fi

echo "========================================================"

# PART 1: Preparação Local (Antigo deploy_full step 1 & 2)
echo ""
echo "📦 [1/4] Atualizando Default Data (JSON)..."
bash "$SCRIPT_DIR/update_default_data.sh"
if [ $? -ne 0 ]; then
    echo "❌ Falha ao atualizar dados padrão. Abortando."
    exit 1
fi

echo ""
echo "🗄️  [2/4] Gerando Backup do Banco (Default State)..."
bash "$SCRIPT_DIR/create_test_db_dump.sh"
if [ $? -ne 0 ]; then
    echo "❌ Falha ao gerar dump do banco. Abortando."
    exit 1
fi

# PART 2: Deploy para Hostinger (Antigo deploy_hostinger logic)
echo ""
echo "🚀 [3/4] Iniciando transferência para $SSH_HOST..."

# 2. Preparar Área Temporária
echo "🧹 Limpando diretório temporário..."
rm -rf "$TEMP_DIR"
mkdir -p "$TEMP_DIR"

# 3. Exportar Banco de Dados Local (Current State for Deploy)
echo "🗄️  Exportando banco de dados atual (via DDEV)..."
DUMP_FILE="$TEMP_DIR/database/deploy_dump_$DATE_NOW.sql.gz"
mkdir -p "$TEMP_DIR/database"

if command -v ddev >/dev/null; then
    cd "$CODE_DIR" || exit 1
    ddev export-db --file "$DUMP_FILE" --gzip=true
    if [ $? -ne 0 ]; then
        echo "⚠️  Aviso: Falha ao exportar banco via DDEV. Continuando sem dump..."
    else
        echo "✅ Banco exportado: $DUMP_FILE"
    fi
else
    echo "⚠️  Aviso: DDEV não encontrado. Pulei o dump do banco."
fi
cd "$SCRIPT_DIR" || exit 1

# 4. Copiar Arquivos do Projeto
echo "📂 Copiando arquivos do projeto..."
rsync -av --exclude '/.git' \
          --exclude '/.ddev' \
          --exclude '/vendor' \
          --exclude '/node_modules' \
          --exclude '/tests' \
          --exclude '/.vscode' \
          "$CODE_DIR/" "$TEMP_DIR/" > /dev/null

# 5. Gerar .env de Produção
echo "⚙️  Gerando arquivo .env de produção..."
mkdir -p "$TEMP_DIR/config"

cat > "$TEMP_DIR/config/.env" <<EOF
CI_ENVIRONMENT = production

app.baseURL = '$SITE_URL'
app.forceGlobalSecureRequests = true

# Configurações de Banco de Dados
DB_HOST = $DB_HOSTNAME
DB_PORT = 3306
DB_DATABASE = $DB_DATABASE
DB_USERNAME = $DB_USERNAME
DB_PASSWORD = $DB_PASSWORD
DB_DRIVER = $DB_DBDRIVER

# Configurações Legado
database.default.hostname = $DB_HOSTNAME
database.default.database = $DB_DATABASE
database.default.username = $DB_USERNAME
database.default.password = $DB_PASSWORD
database.default.DBDriver = $DB_DBDRIVER
database.default.port = 3306

# Segurança
security.tokenName = 'csrf_token'
security.headerName = 'X-CSRF-TOKEN'
security.cookieName = 'csrf_cookie_name'
security.expires = 7200
security.regenerate = true
security.redirect = true

# APIs
GEMINI_API_KEY = '$GEMINI_API_KEY'
EOF

# 6. Enviar Arquivos (Sync Local -> Remote)
echo "📤 Enviando arquivos para o servidor..."
rsync -avz \
    -e "ssh -p $SSH_PORT" \
    --exclude 'public_html/uploads' \
    --exclude '.git' \
    "$TEMP_DIR/" "$SSH_USER@$SSH_HOST:$SSH_PATH/"

if [ $? -ne 0 ]; then
    echo "❌ Erro no envio de arquivos."
    exit 1
fi

# 6.1 Sync de Uploads (Mirror)
echo ""
echo "📂 [4/4] Sincronizando Uploads (Espelho Local -> Remoto)..."
# Sempre deleta remotos que não existem localmente, conforme pedido "deixar idêntico"
rsync -avz --delete \
    -e "ssh -p $SSH_PORT" \
    --exclude '.gitkeep' \
    "$TEMP_DIR/public_html/uploads/" "$SSH_USER@$SSH_HOST:$SSH_PATH/public_html/uploads/"

# 7. Composer Install Remoto
echo "📦 Rodando Composer Install no servidor..."
if [ ! -z "$REMOTE_PHP_PATH" ]; then
    EXPORT_CMD="export PATH=$REMOTE_PHP_PATH:\$PATH &&"
else
    EXPORT_CMD=""
fi

ssh -p "$SSH_PORT" "$SSH_USER@$SSH_HOST" "$EXPORT_CMD cd \"$SSH_PATH\" && (php composer.phar install --no-dev --optimize-autoloader || composer install --no-dev --optimize-autoloader)"

# 8. Importar Banco de Dados
echo "✅ Deploy de Arquivos Concluído!"

if [ -f "$DUMP_FILE" ]; then
    REMOTE_DUMP_PATH="$SSH_PATH/database/$(basename "$DUMP_FILE")"
    
    echo ""
    echo "🔄 IMPORTANDO BANCO DE DADOS EM PRODUÇÃO..."
    
    ssh -p "$SSH_PORT" "$SSH_USER@$SSH_HOST" "zcat $REMOTE_DUMP_PATH | mysql -u $DB_USERNAME -p'$DB_PASSWORD' -h $DB_HOSTNAME $DB_DATABASE"
    
    if [ $? -eq 0 ]; then
            echo "✅ Banco de dados importado com sucesso!"
    else
            echo "❌ FALHA ao importar banco de dados."
    fi
fi

# Limpeza
rm -rf "$TEMP_DIR"

echo ""
echo "========================================================"
echo "✅ UPLOAD PARA PRODUÇÃO CONCLUÍDO!"
echo "   Site: $SITE_URL"
echo "========================================================"
