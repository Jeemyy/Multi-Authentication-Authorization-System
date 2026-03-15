FROM php:8.2-cli

WORKDIR /app

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    default-mysql-client \
    && docker-php-ext-install pdo pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN mkdir -p storage/framework/cache/data \
    && mkdir -p storage/logs \
    && chmod -R 777 storage bootstrap/cache

CMD ["sh", "-c", "sleep 20 && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT"]
