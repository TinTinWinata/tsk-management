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

FROM nginx

COPY .docker/nginx/nginx_template_prod.conf /etc/nginx/conf.d/default.conf

COPY --chown=www-data --from=npm-build /var/www/html/public /var/www/html/public
COPY --chown=www-data --from=npm-build /var/www/html/vendor /var/www/html/vendor
COPY --chown=www-data . /var/www/html

