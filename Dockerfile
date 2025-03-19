FROM php:8.4-cli
RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev \
    unzip \
    zip \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo pdo_mysql gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
COPY . .
RUN chown -R www-data:www-data /var/www/html
RUN composer install --no-dev --optimize-autoloader
CMD ["php", "./src/index.php"]
