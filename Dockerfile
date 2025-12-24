# =============================================================================
# Krayin CRM - Production Dockerfile
# Multi-stage build for optimized image size
# Compatible with Docker Swarm deployment
# =============================================================================

# -----------------------------------------------------------------------------
# Stage 1: Composer dependencies
# -----------------------------------------------------------------------------
FROM composer:2.6 AS composer-builder

WORKDIR /app

# Copy composer files first for better layer caching
COPY composer.json composer.lock ./
COPY packages/ packages/

# Install dependencies without dev packages
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --ignore-platform-reqs

# Copy full application
COPY . .

# Generate optimized autoloader
RUN composer dump-autoload --optimize --no-dev

# -----------------------------------------------------------------------------
# Stage 2: Frontend assets (if needed)
# -----------------------------------------------------------------------------
FROM node:20-alpine AS node-builder

WORKDIR /app

COPY package.json ./
RUN npm install --legacy-peer-deps 2>/dev/null || true

COPY . .
RUN npm run build 2>/dev/null || echo "No build script, skipping..."

# -----------------------------------------------------------------------------
# Stage 3: Production PHP image
# -----------------------------------------------------------------------------
FROM php:8.2-fpm-alpine AS production

LABEL maintainer="DevLead <devlead@krayincrm.com>"
LABEL description="Krayin CRM v2.1.5 with Multi-Theme Login System"
LABEL version="2.1.5"

# Environment variables
ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    COMPOSER_ALLOW_SUPERUSER=1 \
    PHP_OPCACHE_ENABLE=1 \
    PHP_OPCACHE_MEMORY=256 \
    PHP_OPCACHE_MAX_FILES=20000 \
    PHP_MEMORY_LIMIT=512M \
    PHP_MAX_EXECUTION_TIME=300 \
    PHP_UPLOAD_MAX_FILESIZE=64M \
    PHP_POST_MAX_SIZE=64M

# Install system dependencies
RUN apk add --no-cache \
    # Required for PHP extensions
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libwebp-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    libxml2-dev \
    # Required for IMAP
    imap-dev \
    openssl-dev \
    krb5-dev \
    # Required for PDF generation
    fontconfig \
    ttf-freefont \
    # Utilities
    curl \
    git \
    unzip \
    supervisor \
    # MySQL client for healthchecks
    mysql-client \
    # Bash for scripts
    bash

# Configure and install PHP extensions
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
    && docker-php-ext-configure imap \
        --with-kerberos \
        --with-imap-ssl \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        exif \
        gd \
        imap \
        intl \
        mbstring \
        opcache \
        pdo \
        pdo_mysql \
        soap \
        zip \
    && docker-php-ext-enable opcache

# Install Redis extension
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# Copy PHP configuration
COPY docker/php/php.ini /usr/local/etc/php/php.ini
COPY docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Create application user
RUN addgroup -g 1000 krayin \
    && adduser -u 1000 -G krayin -s /bin/bash -D krayin

# Set working directory
WORKDIR /var/www/html

# Copy application from builder stages
COPY --from=composer-builder --chown=krayin:krayin /app /var/www/html
COPY --from=node-builder --chown=krayin:krayin /app/public/build /var/www/html/public/build

# Copy entrypoint and scripts
COPY --chmod=755 docker/scripts/entrypoint.sh /usr/local/bin/entrypoint.sh
COPY --chmod=755 docker/scripts/healthcheck.sh /usr/local/bin/healthcheck.sh

# Create required directories with proper permissions
RUN mkdir -p \
        storage/app/public \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chown -R krayin:krayin /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Supervisor configuration for queue workers
COPY docker/php/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Healthcheck
HEALTHCHECK --interval=30s --timeout=10s --start-period=60s --retries=3 \
    CMD /usr/local/bin/healthcheck.sh

# Expose PHP-FPM port
EXPOSE 9000

# Switch to non-root user
USER krayin

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
