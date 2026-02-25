#!/bin/bash
PROJECT_ROOT="/mnt/d/_WEB/santis"

cd "$PROJECT_ROOT" || exit


ddev start

if [ ! -d "www/vendor" ]; then
    echo "📦 Instalando dependências do Frontend (www)..."
    ddev exec -d /var/www/html/www composer install
fi

echo "======================================================"
echo "✅ WWW:    https://www.santis.ddev.site"
echo "✅ Painel: https://painel.santis.ddev.site"
echo "✅ CDN:    https://cdn.santis.ddev.site"
echo "======================================================"