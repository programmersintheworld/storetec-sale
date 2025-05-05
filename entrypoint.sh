#!/bin/bash

# Crea archivo .env si no existe
if [ ! -f .env ]; then
  echo "APP_KEY=" > .env
fi

# Generar clave y ejecutar migraciones
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan optimize:clear

php artisan key:generate
php artisan migrate --force

# Iniciar Apache
exec apache2-foreground
