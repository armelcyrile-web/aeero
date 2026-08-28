FROM php:8.2-cli

# Installer les dépendances système et l'extension pdo_pgsql pour Neon
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copier le code du projet
COPY . .

# Installer les dépendances Laravel
RUN composer install --no-dev --optimize-autoloader

# Exposer le port 10000
EXPOSE 10000

# Commande de démarrage
CMD php artisan migrate --force && php artisan config:clear && php artisan serve --host=0.0.0.0 --port=10000
