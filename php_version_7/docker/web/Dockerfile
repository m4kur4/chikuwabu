FROM php:7.4-fpm
# install liblary for Laravel
RUN apt-get update && apt-get install -y \
		libfreetype-dev \
		libjpeg62-turbo-dev \
		libpng-dev \
		libonig-dev \
		libxml2-dev \
		zip \
		unzip \
	&& docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-install -j$(nproc) gd pdo_mysql mbstring

# install for xdebug (PHP7.4における最終安定板のverを明示指定)
RUN pecl install xdebug-3.1.6 && docker-php-ext-enable xdebug

# INSTALL composer
# RUN cd /usr/bin \
#     && curl -s http://getcomposer.org/installer | php \
#     && ln -s /usr/bin/composer.phar /usr/bin/composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html