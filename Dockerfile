FROM docker.1panel.live/library/php:8.3-fpm

RUN apt-get update && apt-get install -y \
    && docker-php-ext-install pdo_mysql

WORKDIR /var/www/html

COPY . .

RUN mkdir -p storage/framework/sessions \
    && mkdir -p storage/framework/views \
    && mkdir -p storage/framework/cache \
    && mkdir -p storage/logs \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]