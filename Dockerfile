# Etapa de construcción para instalar dependencias
FROM composer:2.6 as vendor

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist

# Etapa final
FROM php:8.2-apache

# Instala extensiones necesarias
RUN apt-get update && apt-get install -y \
    unzip git curl libpq-dev libonig-dev libzip-dev zip \
    && docker-php-ext-install pdo pdo_pgsql mbstring zip

# Copia archivos
COPY --from=vendor /app /var/www/html
COPY . /var/www/html

# Establece permisos
RUN chown -R www-data:www-data /var/www/html \
    && a2enmod rewrite

# Configura el Virtual Host de Apache
COPY ./docker/apache.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

# Expone el puerto 80
EXPOSE 80

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]

