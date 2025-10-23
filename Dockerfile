FROM php:8.2-fpm-bookworm

# Cài đặt các gói hệ thống và PHP extension cần thiết
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev libicu-dev \
    libjpeg-dev libfreetype6-dev libssl-dev libkrb5-dev libc-client-dev  \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    # Cấu hình IMAP với Kerberos & SSL
    && docker-php-ext-configure imap --with-kerberos --with-imap-ssl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip calendar intl imap \
    && rm -rf /var/lib/apt/lists/*

# Cài Composer (phiên bản 2.5)
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

# Thiết lập thư mục làm việc
WORKDIR /var/www/html

# Copy toàn bộ source code vào container
COPY . .

# Cấp quyền cho storage & bootstrap/cache
RUN chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

# Cổng để PHP-FPM phục vụ nội bộ (cho Nginx proxy)
EXPOSE 9000

CMD ["php-fpm"]