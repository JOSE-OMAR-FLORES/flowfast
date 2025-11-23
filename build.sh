#!/bin/bash
set -e

echo "🚀 Iniciando proceso de build..."

# Instalar dependencias de Composer (sin dev en producción)
echo "📦 Instalando dependencias de Composer..."
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Instalar dependencias de NPM
echo "📦 Instalando dependencias de NPM..."
npm ci --include=dev

# Compilar assets con Vite
echo "🔨 Compilando assets con Vite..."
npm run build

echo "✅ Build completado exitosamente!"
