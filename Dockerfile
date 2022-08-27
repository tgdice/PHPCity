FROM php:7.4-apache

RUN apt-get update && apt-get install -qy \
	build-essential \
	libmcrypt-dev \
	libonig-dev \
	libzip-dev \
	python \
	curl \
	git

RUN docker-php-ext-install mbstring

RUN pecl install ast-1.1.0 && docker-php-ext-enable ast

RUN curl -fsSL https://deb.nodesource.com/setup_12.x | bash -
RUN apt-get install -y nodejs

RUN npm install -g npm
RUN npm install -g bower
RUN npm install -g gulp

## App source
RUN mkdir -p /app

## Apache configs
ENV APACHE_DOCUMENT_ROOT /app/frontend

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}/dist!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}/dist!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

## Enable apache mods
#RUN a2enmod rewrite \
#	headers \
#	deflate \
#	expires \
#	ssl

EXPOSE 80