# Utilise PHP 8.2 avec Apache
FROM php:8.2-apache

# Installe les extensions PHP nécessaires
RUN docker-php-ext-install pdo pdo_pgsql

# Copie tous les fichiers du projet
COPY . /var/www/html

# Définit le dossier public comme racine
WORKDIR /var/www/html/public

# Expose le port 10000 pour Render
EXPOSE 10000

# Commande pour démarrer le serveur PHP
CMD ["php", "-S", "0.0.0.0:10000", "-t", "/var/www/html/public"]
