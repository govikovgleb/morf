FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo_pgsql pgsql zip exif intl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Increase memory limit for Composer (important for low-RAM VPS)
ENV COMPOSER_MEMORY_LIMIT=-1

# Copy composer files and install dependencies WITHOUT scripts
# (artisan is not present yet, post-autoload-dump would fail)
COPY morf_back/composer.json morf_back/composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction

# Copy full application code (now artisan exists)
COPY morf_back .

# Generate optimized autoloader and run post-install scripts
RUN composer dump-autoload --optimize \
    && php artisan package:discover --ansi

# Create storage directories and set permissions
RUN mkdir -p storage/app/public \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/testing \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Create storage symlink
RUN php artisan storage:link

USER www-data

EXPOSE 9000
CMD ["php-fpm"]
