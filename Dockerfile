# Utilise PHP 8.2 avec Apache et PostgreSQL client déjà installé
FROM php:8.2-apache

# Installer les dépendances nécessaires pour pdo_pgsql
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

# Copier tous les fichiers du projet
COPY . /var/www/html

# Définir le dossier public comme racine
WORKDIR /var/www/html/public

# Expose le port 10000 pour Render
EXPOSE 10000

# Commande pour démarrer le serveur PHP
CMD ["php", "-S", "0.0.0.0:10000", "-t", "/var/www/html/public"]
