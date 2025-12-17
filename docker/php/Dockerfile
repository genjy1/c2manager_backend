# Build stage для Node.js зависимостей
FROM node:20-alpine AS node-builder

WORKDIR /app

COPY package*.json ./
RUN npm ci --only=production

COPY . .
RUN npm run build

# PHP stage
FROM php:8.4-fpm-alpine AS base

# Установка системных зависимостей
RUN apk add --no-cache \
    git \
    zip \
    unzip \
    curl \
    libzip-dev \
    libpq-dev \
    oniguruma-dev \
    libxml2-dev \
    sqlite-dev \
    icu-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    autoconf \
    g++ \
    make \
    linux-headers

# PHP расширения
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        zip \
        opcache \
        intl \
        gd \
        bcmath \
        pcntl

# Redis extension
RUN pecl install redis \
    && docker-php-ext-enable redis

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Рабочая директория
WORKDIR /var/www/html

# Development stage
FROM base AS development

# php.ini для development
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

# Создание пользователя для Laravel
RUN addgroup -g 1000 laravel && \
    adduser -D -u 1000 -G laravel laravel

# Права доступа
RUN chown -R laravel:laravel /var/www/html

USER laravel

# Production stage
FROM base AS production

# Оптимизированный php.ini для production
RUN echo "display_errors=Off" >> /usr/local/etc/php/conf.d/production.ini \
    && echo "error_reporting=E_ALL & ~E_DEPRECATED & ~E_STRICT" >> /usr/local/etc/php/conf.d/production.ini \
    && echo "memory_limit=512M" >> /usr/local/etc/php/conf.d/production.ini \
    && echo "upload_max_filesize=20M" >> /usr/local/etc/php/conf.d/production.ini \
    && echo "post_max_size=20M" >> /usr/local/etc/php/conf.d/production.ini \
    && echo "max_execution_time=60" >> /usr/local/etc/php/conf.d/production.ini

# OPcache настройки для production
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=256" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.interned_strings_buffer=16" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=20000" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.fast_shutdown=1" >> /usr/local/etc/php/conf.d/opcache.ini

# Создание пользователя для Laravel
RUN addgroup -g 1000 laravel && \
    adduser -D -u 1000 -G laravel laravel

# Копируем зависимости Composer
COPY --chown=laravel:laravel composer.json composer.lock ./

# Установка зависимостей без dev пакетов
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --optimize-autoloader

# Копируем приложение
COPY --chown=laravel:laravel . .

# Копируем собранные Node.js ресурсы
COPY --from=node-builder --chown=laravel:laravel /app/public/build ./public/build

# Генерация autoloader и оптимизация
RUN composer dump-autoload --optimize --classmap-authoritative

# Права доступа
RUN chown -R laravel:laravel /var/www/html \
    && chmod -R 755 storage bootstrap/cache

USER laravel

EXPOSE 9000
