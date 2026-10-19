
FROM richarvey/nginx-php-fpm:3.1.6

WORKDIR /var/www/html

COPY . .

ENV WEBROOT=/var/www/html/public
ENV PHP_ERRORS_STDERR=1
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV LOG_CHANNEL=stderr

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN mkdir -p storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

RUN chmod -R 775 storage bootstrap/cache

# Remove old Nginx default configuration
RUN rm -f /etc/nginx/sites-enabled/default \
    /etc/nginx/sites-enabled/default.conf

# Install Laravel Nginx configuration
COPY nginx.conf /etc/nginx/sites-enabled/pennypilot.conf

# Copy Render startup script
COPY start-render.sh /usr/local/bin/start-render.sh

RUN chmod +x /usr/local/bin/start-render.sh

CMD ["/usr/local/bin/start-render.sh"]
