FROM php:8.3-cli

RUN pecl install redis-5.3.7 \
    && pecl install xdebug-3.2.1 \
    && docker-php-ext-enable pdo_mysql

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
