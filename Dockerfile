FROM php:8.2-cli

# install dependencies + tesseract
RUN apt-get update && apt-get install -y \
    tesseract-ocr \
    tesseract-ocr-eng \
    tesseract-ocr-ind \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip unzip git curl \
    && docker-php-ext-install pdo pdo_mysql

# composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY ./be /app

RUN composer install --no-dev --optimize-autoloader

# permission fix
RUN chmod -R 777 storage bootstrap/cache

# start command
CMD sh -c "sleep 5 && php artisan config:clear && php artisan config:cache && php artisan migrate --force && php artisan storage:link && php artisan serve --host=0.0.0.0 --port=8000"