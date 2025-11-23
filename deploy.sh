#!/bin/bash
set -e

echo "🔄 Ejecutando migraciones..."
php artisan migrate --force

echo "⚡ Optimizando aplicación..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

echo "✅ Despliegue completado!"
