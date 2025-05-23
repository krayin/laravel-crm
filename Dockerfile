FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    default-mysql-client \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
    libicu-dev libjpeg-dev libfreetype6-dev libcurl4-openssl-dev \
    netcat-openbsd \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql intl zip gd calendar \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite

COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

COPY composer.json composer.lock ./
COPY . .

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

RUN chmod +x /var/www/html/scripts/entrypoint.sh

CMD ["./scripts/entrypoint.sh"]
