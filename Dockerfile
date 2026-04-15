FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git curl zip unzip tesseract-ocr \
    libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring bcmath gd zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY ./be /app

# bersihin cache zombie
RUN rm -rf bootstrap/cache/*.php

# bunuh telescope
RUN rm -f app/Providers/TelescopeServiceProvider.php

# install dependency
RUN composer install --no-dev --optimize-autoloader --no-scripts

# permission
RUN chmod -R 777 storage bootstrap/cache

EXPOSE 8080

CMD php artisan serve --host=0.0.0.0 --port=8080