FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libpng-dev libjpeg-dev libfreetype6-dev \
    nodejs npm \
    tesseract-ocr tesseract-ocr-eng tesseract-ocr-ind \
    libpq-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# 🔥 FIX PATH
COPY ./be /app

# backend
RUN composer install --no-dev --optimize-autoloader

# frontend
RUN npm install
RUN npm run build

RUN php artisan config:clear && \
    php artisan config:cache

RUN chmod -R 775 storage bootstrap/cache

CMD sh -c "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"