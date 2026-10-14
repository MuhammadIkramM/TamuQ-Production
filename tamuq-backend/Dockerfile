FROM php:8.2-cli

COPY --from=ghcr.io/mlocati/php-extension-installer:latest /usr/bin/install-php-extensions /usr/local/bin/

RUN apt-get update && apt-get install -y git unzip \
    && rm -rf /var/lib/apt/lists/* \
    && install-php-extensions pdo_mysql mbstring zip gd bcmath exif pcntl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html