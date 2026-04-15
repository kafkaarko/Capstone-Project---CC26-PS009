FROM php:8.4-cli

# install system deps + node + tesseract
RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libpng-dev libjpeg-dev libfreetype6-dev \
    nodejs npm \
    tesseract-ocr tesseract-ocr-eng tesseract-ocr-ind \
    libpq-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql

# install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# copy project
COPY . .

# install backend deps
RUN composer install --no-dev --optimize-autoloader

# install frontend deps + build vite
RUN npm install
RUN npm run build

# laravel optimization
RUN php artisan config:clear && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# permission fix
RUN chmod -R 775 storage bootstrap/cache

# start app
CMD sh -c "php artisan migrate --force && php artisan storage:link && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"