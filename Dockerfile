# ==============================================================================
# Stage 1: Build Frontend Assets (Vite + Vue 3)
# ==============================================================================
FROM node:20-alpine AS frontend
WORKDIR /app

COPY package*.json ./
RUN npm ci || npm install

COPY . .
RUN npm run build

# ==============================================================================
# Stage 2: Production PHP + Nginx Environment
# ==============================================================================
FROM php:8.3-fpm-alpine

# Set environment
ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    COMPOSER_ALLOW_SUPERUSER=1

# Install system dependencies & build tools
RUN apk add --no-cache \
    bash \
    nginx \
    supervisor \
    curl \
    git \
    libpq \
    libzip \
    icu-libs \
    freetype \
    libjpeg-turbo \
    libpng \
    oniguruma \
    && apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    postgresql-dev \
    libzip-dev \
    icu-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    oniguruma-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    pdo_pgsql \
    pgsql \
    intl \
    zip \
    opcache \
    bcmath \
    gd \
    pcntl \
    mbstring \
    && apk del .build-deps

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy configuration files
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Copy compiled frontend assets from Stage 1
COPY --from=frontend /app/public/build ./public/build

# Install PHP dependencies without dev dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Set file permissions for Laravel storage and cache
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
