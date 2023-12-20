FROM php:8.2-apache
WORKDIR /var/www/html
COPY . /var/www/html


RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN docker-php-ext-install opcache
RUN docker-php-ext-enable opcache

RUN apt-get update \
    && apt-get install -y default-mysql-client htop nano lsof
#RUN apt-get install php8.0 libapache2-mod-php8.0
EXPOSE 8081
#RUN mkdir /run/apache2
RUN a2enmod rewrite

COPY ./docker/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY ./docker/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini
