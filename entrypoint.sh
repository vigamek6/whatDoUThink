#!/bin/sh
# entrypoint.sh - Dynamic PORT binding for Render / Cloud deploy
PORT="${PORT:-80}"
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT}>/g" /etc/apache2/sites-available/000-default.conf

# Ensure permissions
chown -R www-data:www-data /var/www/html/sessions /var/www/html/database.sqlite 2>/dev/null || true

exec apache2-foreground
