#!/bin/bash

echo "🚀 Rozpoczynam wdrażanie aplikacji..."

# 1. Pobranie najnowszej wersji kodu z repozytorium
git pull origin main

npm install
npm run build

# 2. Budowanie i uruchamianie kontenerów (w tle)
# --build wymusza przebudowanie obrazu, jeśli zmieniłeś np. Dockerfile
docker compose up -d --build

# 3. Instalacja zależności PHP wewnątrz kontenera
docker compose exec app composer install --no-dev --optimize-autoloader

# 4. Upewnienie się, że baza danych SQLite istnieje i ma uprawnienia
docker compose exec app touch database/database.sqlite
docker compose exec app chown -R www-data:www-data database storage bootstrap/cache

# 5. Uruchomienie migracji bazy danych
# --force jest niezbędne na produkcji
docker compose exec app php artisan migrate --force

# 6. Optymalizacja cache Laravela
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache

# 7. Opcjonalnie: Przebudowanie assetów (jeśli nie używasz gotowych obrazów)
# docker compose exec app npm install && npm run build

echo "✅ Wdrożenie zakończone sukcesem!