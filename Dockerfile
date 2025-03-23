FROM php:8.4-cli-alpine

RUN apk update && apk add --no-cache \
    libzip-dev \
    unzip \
    zip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) pdo pdo_mysql gd zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN chown -R www-data:www-data /var/www/html

RUN composer install --no-dev --optimize-autoloader

CMD ["php", "./src/index.php"]
