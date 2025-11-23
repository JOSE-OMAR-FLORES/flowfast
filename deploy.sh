#!/bin/bash
set -e

echo "🧹 Limpiando caché..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "🔗 Creando link de storage..."
php artisan storage:link || true

echo "🔄 Ejecutando migraciones..."
php artisan migrate --force

echo "⚡ Optimizando aplicación..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

echo "✅ Despliegue completado!"
