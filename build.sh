#!/bin/bash
set -e

echo "🚀 Iniciando proceso de build..."

# Limpiar caché de configuración
echo "🧹 Limpiando caché..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Instalar dependencias de Composer (sin dev en producción)
echo "📦 Instalando dependencias de Composer..."
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Instalar dependencias de NPM
echo "📦 Instalando dependencias de NPM..."
npm ci --include=dev

# Compilar assets con Vite
echo "🔨 Compilando assets con Vite..."
npm run build

# Crear link simbólico de storage
echo "🔗 Creando link de storage..."
php artisan storage:link || true

echo "✅ Build completado exitosamente!"
