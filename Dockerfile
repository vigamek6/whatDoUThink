FROM php:8.3-apache

# Install required dependencies and PHP PDO extensions (MySQL + SQLite)
RUN apt-get update && apt-get install -y --no-install-recommends \
    libsqlite3-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Copy all source files
COPY . /var/www/html/

# Setup directories and permissions
RUN mkdir -p /var/www/html/sessions \
    && touch /var/www/html/database.sqlite \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/sessions \
    && chmod 664 /var/www/html/database.sqlite \
    && chmod +x /var/www/html/entrypoint.sh

# Expose ports (80 for local docker, Render assigns $PORT dynamically)
EXPOSE 80 10000

CMD ["/var/www/html/entrypoint.sh"]
