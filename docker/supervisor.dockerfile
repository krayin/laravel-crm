FROM php:8.1.11RC1-fpm-alpine

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

RUN apk update && apk add --no-cache supervisor

RUN mkdir -p "/etc/supervisor/logs"

RUN mkdir -p "/var/www/html/storage/logs"

RUN mkdir -p "/var/www/html/vendor"

COPY ./ /var/www/html/

COPY ./docker/supervisord.conf /etc/supervisor/supervisord.conf

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

CMD ["/usr/bin/supervisord", "-n", "-c",  "/etc/supervisor/supervisord.conf"]
