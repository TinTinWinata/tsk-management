# Composer dependencies

FROM composer AS composer-build

WORKDIR /var/www/html

COPY composer.json composer.lock /var/www/html

RUN mkdir -p /var/www/html/database/{factories,seeds} \
&& composer install --no-dev --prefer-dist --no-scripts --optimize-autoloader --no-progress --ignore-platform-reqs 

# NPM dependencies

FROM node AS npm-build

WORKDIR /var/www/html

## Copy vendor folder from composer-build stage
COPY --from=composer-build /var/www/html/vendor /var/www/html/vendor

COPY package.json package-lock.json tsconfig.json vite.config.js /var/www/html
COPY postcss.config.js tailwind.config.js /var/www/html

COPY resources /var/www/html/resources/

COPY public /var/www/html/public/

RUN npm ci
RUN npm run build


# Production image

FROM php:8.2-fpm

WORKDIR /var/www/html

EXPOSE 9000

RUN apt-get update && apt-get install --quiet --yes --no-install-recommends \
	libzip-dev \
	unzip \
	&& docker-php-ext-install opcache zip pdo pdo_mysql

# Use the default production configuration
RUN mv $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini

# Override with custom opcache settings
COPY .docker/php/opcache.ini $PHP_INI_DIR/conf.d/


COPY --from=composer /usr/bin/composer /usr/bin/composer


COPY --chown=www-data --from=composer-build /var/www/html/vendor /var/www/html/vendor
COPY --chown=www-data --from=npm-build /var/www/html/public /var/www/html/public
COPY --chown=www-data . /var/www/html

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer dump -o && composer check-platform-reqs
