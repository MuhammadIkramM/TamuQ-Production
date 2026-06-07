# Gunakan PHP 8.2 dengan Apache
FROM php:8.2-apache

# Install driver database (PostgreSQL) dan tools zip
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql zip

# Aktifkan mod_rewrite untuk URL Laravel yang cantik
RUN a2enmod rewrite

# Arahkan Apache ke folder public Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set folder kerja
WORKDIR /var/www/html

# Copy semua file project ke dalam container
COPY . .

# Install dependensi Laravel via Composer
RUN composer install --no-dev --optimize-autoloader

# Atur hak akses folder storage agar bisa ditulisi
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
