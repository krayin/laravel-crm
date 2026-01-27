FROM php:8.2-fpm AS base

# Install System Dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libpng-dev libjpeg-dev \
    libfreetype6-dev libonig-dev libxml2-dev libicu-dev \
    zlib1g-dev ca-certificates procps \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install -j$(nproc) pdo_mysql mbstring exif pcntl bcmath gd zip intl sockets xml opcache calendar \
    && pecl install redis && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# 1. Copy composer files first for layer caching
COPY composer.json composer.lock ./

# 2. Install dependencies WITHOUT scripts (prevents artisan 'missing' errors)
RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist

# 3. Copy the rest of the application
COPY . .

# 4. Create the .env file if it doesn't exist (required for artisan to run)
# We use the example file as a placeholder during the build
RUN if [ ! -f .env ]; then cp .env.example .env; fi

# 5. Finalize Composer & Optimize
# This runs dump-autoload AND triggers the Laravel package discovery
RUN composer dump-autoload --optimize --no-dev

# 6. Clear and Optimize Laravel
RUN php artisan optimize:clear && php artisan optimize

# 7. Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 8. Entrypoint setup
COPY ./docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]