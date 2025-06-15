FROM php:8.1.11RC1-fpm-alpine

RUN set -ex \
	&& apk add --update --no-cache \
		postgresql-dev \
		git libzip-dev freetype \
		libpng libjpeg-turbo freetype-dev \
		libpng-dev libjpeg-turbo-dev libwebp-dev \
		libevent-dev \
    && docker-php-ext-configure intl \
	&& docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    --with-webp 

RUN docker-php-ext-install pdo_pgsql intl gd zip sockets

#RUN pecl install event 

#RUN docker-php-ext-enable event

COPY ./ /var/www/

WORKDIR /var/www

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN composer install

CMD ["php", "artisan", "websockets:serve"]
