FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libzip-dev \
    zip \
    nodejs \
    npm

RUN docker-php-ext-install pdo pdo_mysql zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN npm install && npm run build

RUN touch database/database.sqlite && \
    php artisan migrate --force && \
    php artisan db:seed --force

RUN php artisan config:clear && \
    php artisan cache:clear && \
    php artisan view:clear

EXPOSE 10000

CMD php -S 0.0.0.0:10000 -t public