# PHPlexus/Dockerfile
FROM php:8.1-apache

# Enable mod_rewrite for Apache
RUN a2enmod rewrite

# Install utilities, extensions, and composer
RUN apt-get update && apt-get install -y \
    git \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install zip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the document root to /var/www/html/public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

# Copy application files
COPY . /var/www/html

WORKDIR /var/www/html

# Set permissions for the log directory
RUN chown -R www-data:www-data /var/www/html/logs && chmod -R 777 /var/www/html/logs

# Install composer dependencies
RUN composer install --no-dev --optimize-autoloader
