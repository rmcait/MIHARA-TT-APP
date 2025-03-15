FROM php:8.2-fpm

# 必要な拡張モジュールとツールをインストール
RUN apt-get update && apt-get install -y \
    git unzip zip curl libzip-dev \
    && docker-php-ext-install pdo_mysql zip

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html