FROM php:8.2-fpm

# Install system deps and PHP extensions
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libpq-dev \
        unzip \
        git \
        libzip-dev \
        libpng-dev \
        libjpeg-dev \
        libwebp-dev \
        libfreetype6-dev \
        libonig-dev \
        zip \
    && docker-php-ext-install pdo pdo_pgsql mbstring zip gd opcache \
    && pecl install redis || true \
    && docker-php-ext-enable redis || true \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy app
COPY . /var/www/html

# Install PHP deps
RUN composer install --no-interaction --no-scripts --prefer-dist --optimize-autoloader || true

# Permissions for uploads
RUN mkdir -p public/uploads/books && chown -R www-data:www-data public/uploads

EXPOSE 9000
CMD ["php-fpm"]
