FROM node:18-alpine AS deps
WORKDIR /app
COPY . .
RUN npm install
RUN npm run build

FROM php:8.2.11RC1-fpm-alpine
RUN set -ex \
	&& apk add --update --no-cache \
		postgresql-dev \
		git libzip-dev freetype \
		libpng libjpeg-turbo freetype-dev \
		libpng-dev libjpeg-turbo-dev libwebp-dev \
    && docker-php-ext-configure intl \
	&& docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    --with-webp 

	
RUN docker-php-ext-install pdo_pgsql intl gd zip

RUN apk add --no-cache \
    mysql-client \
    mysql-dev \
    && docker-php-ext-install pdo_mysql

RUN docker-php-ext-install calendar


RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN mkdir -p /var/www/app/vendor

WORKDIR /var/www/app

COPY . .

COPY --from=deps /app/public/build ./public/build

RUN chown -R www-data:www-data storage/ bootstrap/ public/ vendor/

USER www-data
