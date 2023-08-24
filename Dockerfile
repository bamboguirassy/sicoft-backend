FROM php:7.2-apache
RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libzip-dev \
    unzip
RUN docker-php-ext-install pdo pdo_mysql zip

WORKDIR /var/www/html
COPY . /var/www/html
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 775 /var/www/html
# RUN chmod -R 777 /var/www/html/var

