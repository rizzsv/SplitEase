FROM php:8.2-apache

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install ekstensi & dependency Laravel + Filament
RUN apt-get update && apt-get install -y \
    zip unzip git curl libzip-dev libonig-dev libicu-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql zip intl mbstring \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Set document root ke folder public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Update konfigurasi Apache agar root-nya ke public/
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Aktifkan mod_rewrite agar routing Laravel bisa jalan
RUN a2enmod rewrite
