FROM php:8.2-fpm

# System deps
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev

# PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# 🔥 ندخلو الكود داخل image
COPY . /var/www

# 🔥 install production deps فقط
RUN composer install --no-dev --optimize-autoloader

# Permissions
RUN chown -R www-data:www-data \
    /var/www/storage \
    /var/www/bootstrap/cache
