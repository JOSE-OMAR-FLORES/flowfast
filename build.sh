#!/bin/bash
set -e

echo "🚀 Iniciando proceso de build..."

# Instalar dependencias de Composer (sin dev en producción)
echo "📦 Instalando dependencias de Composer..."
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Instalar dependencias de NPM
echo "📦 Instalando dependencias de NPM..."
npm ci --include=dev

# Verificar versión de Node
echo "📋 Versión de Node: $(node -v)"
echo "📋 Versión de NPM: $(npm -v)"

# Limpiar caché de Vite
echo "🧹 Limpiando caché de Vite..."
rm -rf node_modules/.vite

# Compilar assets con Vite
echo "🔨 Compilando assets con Vite..."
NODE_ENV=production npm run build

# Verificar que se generaron los assets
echo "✅ Verificando assets compilados..."
if [ -f "public/build/manifest.json" ]; then
    echo "✅ manifest.json generado correctamente"
    ls -lh public/build/
else
    echo "❌ ERROR: manifest.json no se generó"
    exit 1
fi

echo "✅ Build completado exitosamente!"
