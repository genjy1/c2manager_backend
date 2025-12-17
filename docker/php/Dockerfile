FROM php:8.4-fpm

# Рабочая директория
WORKDIR /var/www/html

# Системные зависимости
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    curl \
    libzip-dev \
    libpq-dev \
    libonig-dev \
    libxml2-dev \
    libsqlite3-dev \
    ca-certificates \
    --no-install-recommends \
    && rm -rf /var/lib/apt/lists/*

# PHP расширения
RUN docker-php-ext-install pdo pdo_mysql zip

# Node.js (если реально нужен)
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && node -v \
    && npm -v

# Composer
RUN curl -sS https://getcomposer.org/installer | php \
    -- --install-dir=/usr/local/bin --filename=composer

# php.ini
COPY php.ini /usr/local/etc/php/conf.d/custom.ini
