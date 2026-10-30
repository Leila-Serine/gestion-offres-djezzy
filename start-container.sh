#!/usr/bin/env bash
set -euo pipefail
cd /app

# Le volume Railway ne contient pas les répertoires créés au build.
mkdir -p storage/app/private storage/app/public storage/framework/cache/data \
    storage/framework/sessions storage/framework/views storage/logs bootstrap/cache

# Toujours recharger les variables du déploiement AVANT de toucher à la base.
# Ne pas utiliser optimize:clear : il vide aussi le store de cache applicatif.
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear

if [ -z "${APP_KEY:-}" ]; then
    echo "APP_KEY manquante : définir une clé persistante dans Railway." >&2
    exit 1
fi

if [ "${RAILPACK_SKIP_MIGRATIONS:-false}" != "true" ]; then
    php artisan migrate --force
fi

# Aucun seeder ni compte de test lancé automatiquement.
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "Starting Laravel server ..."
exec docker-php-entrypoint --config /Caddyfile --adapter caddyfile
