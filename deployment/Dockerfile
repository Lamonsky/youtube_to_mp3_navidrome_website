# Wybierz oficjalny obraz PHP z rozszerzeniami potrzebnymi dla Laravel
FROM php:8.2-fpm

# Instalacja zależności systemowych i PHP
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instalacja Composera
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Ustaw katalog roboczy
WORKDIR /var/www

# Kopiuj pliki aplikacji (poza vendor i node_modules)
COPY . .

# Instalacja zależności aplikacji
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Ustaw uprawnienia do storage i bootstrap/cache
RUN chown -R www-data:www-data storage bootstrap/cache

# Ustawienia środowiska
ENV APP_ENV=production

# Otwórz port (jeśli korzystasz z built-in serwera, np. php artisan serve)
EXPOSE 9000

# Domyślna komenda
CMD ["php-fpm"]

# W docker-compose.yml mapuj ./storage/app/public na zewnątrz, np.:
# volumes:
#   - ./storage/app/public:/var/www/storage/app/public