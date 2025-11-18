# PHP-FPM untuk Nginx
FROM php:8.2-fpm

# Install ekstensi MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Set working directory di dalam kontainer
WORKDIR /var/www/vehiclewash

# Set permissions
RUN chown -R www-data:www-data /var/www/vehiclewash

# Expose port 9000
EXPOSE 9000
