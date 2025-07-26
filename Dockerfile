# Pakai image PHP bawaan dengan Apache
FROM php:8.2-apache

# Copy semua file project ke folder web server container
COPY . /var/www/html/

# Buka port 80 (default HTTP)
EXPOSE 80
