# Etapa 1: Dependencias PHP
FROM composer:2.6 as vendor

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist

# Etapa 2: Compilar assets con Node y Vite
FROM node:20 as frontend

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm install
COPY . ./
COPY --from=vendor /app/vendor ./vendor
RUN npm run build

# Etapa 3: Imagen final con PHP + Apache
FROM php:8.2-apache

# Añadir Node.js en la imagen final si necesitas hacer build ahí
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Instalar extensiones de PHP necesarias
RUN apt-get update && apt-get install -y \
    unzip git curl libpq-dev libonig-dev libzip-dev zip \
    && docker-php-ext-install pdo pdo_pgsql mbstring zip

# Configurar Apache
COPY ./docker/apache.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

# Copiar código del backend (Laravel) desde la etapa vendor
COPY --from=vendor /app /var/www/html

# Copiar archivos restantes (por si hay más cosas)
COPY . /var/www/html

# Copiar el build generado con Vite
COPY --from=frontend /app/public/build /var/www/html/public/build

# Permisos correctos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

WORKDIR /var/www/html

# Entrypoint y Apache
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80
ENTRYPOINT ["/entrypoint.sh"]
