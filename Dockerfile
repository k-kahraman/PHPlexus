# PHPlexus/Dockerfile
FROM php:8.3-apache

# Configure Apache port to 8080 (non-privileged) for non-root execution
RUN sed -s -i -e "s/Listen 80/Listen 8080/g" /etc/apache2/ports.conf \
    && sed -s -i -e "s/:80/:8080/g" /etc/apache2/sites-available/*.conf

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Disable Server Signatures & Tokens for hardening
RUN echo "ServerTokens ProductOnly" >> /etc/apache2/apache2.conf \
    && echo "ServerSignature Off" >> /etc/apache2/apache2.conf

# Install required system packages
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    libzip-dev \
    zip \
    unzip \
    curl \
    && docker-php-ext-install zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the document root
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

WORKDIR /var/www/html

# Optimize Docker layer caching: install dependencies first
COPY composer.json composer.lock* ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy the rest of the application files
COPY . .

# Finish composer autoload optimization
RUN composer dump-autoload --no-dev --classmap-authoritative

# Configure Apache permissions to run as non-root user (www-data)
RUN chown -R www-data:www-data /var/www/html \
    && chown -R www-data:www-data /var/run/apache2 /var/lock/apache2 /var/log/apache2

# Expose non-privileged port
EXPOSE 8080

# Switch to non-root user
USER www-data

# Container Healthcheck
HEALTHCHECK --interval=30s --timeout=5s --start-period=5s --retries=3 \
  CMD curl -f http://localhost:8080/ || exit 1
