FROM php:8.4-fpm-alpine

# Instalacja rozszerzeń (SQLite jest wbudowane w PHP, ale potrzebujemy pdo_sqlite)
RUN apk add --no-cache \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    sqlite-dev \
    libxml2-dev \
    oniguruma-dev \
    curl-dev \
    nodejs \
    npm \
    python3 \
    py3-pip \
    ffmpeg \
    yt-dlp

RUN docker-php-ext-install pdo_sqlite bcmath gd zip pcntl mbstring xml

WORKDIR /var/www

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

# Tworzymy plik bazy danych, jeśli nie istnieje i nadajemy uprawnienia
RUN mkdir -p database && touch database/database.sqlite
RUN chmod -R 775 storage bootstrap/cache database
RUN chown -R www-data:www-data /var/www

RUN npm install
RUN npm run build

RUN composer install --no-dev --optimize-autoloader --no-interaction --verbose

EXPOSE 9000
CMD ["php-fpm"]