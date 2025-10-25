# Laravel PHP-FPM Container
FROM php:8.3-fpm

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim unzip git curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip exif \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copy existing app
COPY . .

# Copy custom PHP config
COPY .docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Permissions for Laravel storage & bootstrap
RUN chown -R www-data:www-data storage bootstrap/cache && chmod -R 775 storage bootstrap/cache

# Entrypoint script
COPY .docker/start-container.sh /usr/local/bin/start-container.sh
RUN chmod +x /usr/local/bin/start-container.sh

EXPOSE 9000
CMD ["start-container.sh"]

