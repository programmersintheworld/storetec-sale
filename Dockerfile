# Etapa 1: Instala dependencias PHP
FROM composer:2.6 as vendor

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist


# Etapa 2: Compila assets de Vite con Node
FROM node:20 as frontend

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm install

COPY . ./

# 👇 Añadimos vendor desde la etapa de composer
COPY --from=vendor /app/vendor ./vendor

RUN npm run build


# Etapa final: imagen PHP + Apache + Laravel
FROM php:8.2-apache

# Instala extensiones necesarias
RUN apt-get update && apt-get install -y \
    unzip git curl libpq-dev libonig-dev libzip-dev zip \
    && docker-php-ext-install pdo pdo_pgsql mbstring zip

# Configura Apache
COPY ./docker/apache.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

# Copia archivos backend desde vendor
COPY --from=vendor /app /var/www/html
COPY . /var/www/html

# Copia el build de Vite
COPY --from=frontend /app/public/build /var/www/html/public/build

# Permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

WORKDIR /var/www/html

# Puerto expuesto
EXPOSE 80

# Entrypoint
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]