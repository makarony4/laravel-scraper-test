FROM php:8.2-fpm

RUN apt update && apt upgrade -y && apt install -y git zip libzip-dev libpng-dev libfreetype-dev librabbitmq-dev libjpeg62-turbo-dev libicu-dev supervisor libssh-dev \
 \
#    && pecl install xdebug \
    && docker-php-ext-configure intl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        opcache \
        pdo_mysql \
        sockets \
        zip \
        gd \
        intl
#    && pecl install amqp \
#    && docker-php-ext-enable amqp

#RUN docker-php-ext-enable xdebug
#
#RUN echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/xdebug.ini \
# && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/xdebug.ini \
# && echo "xdebug.log=/var/log/xdebug/xdebug.log" >> /usr/local/etc/php/conf.d/xdebug.ini \
# && echo "xdebug.discover_client_host=1" >> /usr/local/etc/php/conf.d/xdebug.ini \
# && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/xdebug.ini \
# && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/xdebug.ini

#RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash \
#    && apt install symfony-cli

RUN  curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --2.2
RUN #composer global require laravel/installer


WORKDIR /var/www

COPY ./ .
