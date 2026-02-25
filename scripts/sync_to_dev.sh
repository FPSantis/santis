#!/bin/bash

# ==============================================================================
# Script de Sincronização Produção -> Desenvolvimento
# 1. Sync Uploads (Remoto -> Local)
# 2. Dump Banco Remoto -> Import Local (DDEV)
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
TEMP_DIR="$SCRIPT_DIR/sync_temp"
DATE_NOW=$(date +%Y%m%d_%H%M%S)

echo "🚀 INICIANDO SINCRONIZAÇÃO PROD -> DEV..."
echo "========================================================"
echo "⚠️  ATENÇÃO: ISSO IRÁ SOBRESCREVER SEU BANCO DE DADOS LOCAL E ARQUIVOS DE UPLOAD!"
echo "   Você tem certeza que deseja continuar?"
read -p "   (y/n)? " -n 1 -r
echo ""
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "❌ Abortado pelo usuário."
    exit 1
fi

echo "========================================================"

# 2. Sync Uploads (Remoto -> Local)
echo ""
echo "📂 [1/2] Sincronizando Uploads (Remoto -> Local)..."
echo "   Origem: $SSH_USER@$SSH_HOST:$SSH_PATH/public_html/uploads/"
echo "   Destino: $CODE_DIR/public_html/uploads/"

# Garantir que diretório local existe
mkdir -p "$CODE_DIR/public_html/uploads"

# Rsync reverso (Delete local files not in remote)
rsync -avz --delete \
    -e "ssh -p $SSH_PORT" \
    --exclude '.gitkeep' \
    "$SSH_USER@$SSH_HOST:$SSH_PATH/public_html/uploads/" \
    "$CODE_DIR/public_html/uploads/"

if [ $? -ne 0 ]; then
    echo "❌ Erro ao sincronizar uploads."
    exit 1
fi
echo "✅ Uploads sincronizados."

# 3. Sync Banco de Dados
echo ""
echo "🗄️  [2/2] Sincronizando Banco de Dados..."

# Preparar diretório temp
rm -rf "$TEMP_DIR"
mkdir -p "$TEMP_DIR"

REMOTE_DUMP_FILE="prod_dump_$DATE_NOW.sql.gz"
LOCAL_DUMP_FILE="$TEMP_DIR/$REMOTE_DUMP_FILE"

echo "   3.1 Gerando dump no servidor..."
# Executa mysqldump no servidor e salva em arquivo temporário lá (ou pipe direto se preferir, mas arquivo é mais debugável)
# Vamos usar pipe direto para evitar ocupar espaço no servidor e simplificar permissões
# Mas mysqldump direto via SSH as vezes é chato com senha. 
# O config tem senha.
# Melhor approach: mysqldump no servidor > gzip > stdout > local file

ssh -p "$SSH_PORT" "$SSH_USER@$SSH_HOST" "mysqldump -u $DB_USERNAME -p'$DB_PASSWORD' -h $DB_HOSTNAME $DB_DATABASE | gzip" > "$LOCAL_DUMP_FILE"

if [ $? -ne 0 ] || [ ! -s "$LOCAL_DUMP_FILE" ]; then
    echo "❌ Erro ao baixar dump do banco de dados (Arquivo vazio ou erro no SSH)."
    # Tenta limpar
    rm -f "$LOCAL_DUMP_FILE"
    exit 1
fi

echo "   ✅ Dump baixado: $LOCAL_DUMP_FILE"

# Importar no DDEV
if command -v ddev >/dev/null; then
    echo "   3.2 Importando para DDEV..."
    cd "$CODE_DIR" || exit 1
    
    # Importar
    ddev import-db --src="$LOCAL_DUMP_FILE"
    
    if [ $? -ne 0 ]; then
        echo "❌ Falha ao importar banco no DDEV."
        exit 1
    fi
    echo "   ✅ Banco importado no DDEV!"
else
    echo "⚠️  DDEV não encontrado. O dump está salvo em $LOCAL_DUMP_FILE mas não foi importado."
fi

# Limpeza
rm -rf "$TEMP_DIR"

echo ""
echo "========================================================"
echo "✅ SINCRONIZAÇÃO PROD -> DEV CONCLUÍDA!"
echo "   Uploads espelhados e Banco atualizado."
echo "========================================================"
