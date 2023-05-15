#####################
### Scratch image ###
#####################
FROM php:8.2.4-fpm-alpine3.17 AS scratch

RUN set -x \
    && apk add --no-cache --virtual .build-deps icu-dev linux-headers \
    && docker-php-ext-install -j$(nproc) intl \
    && docker-php-ext-install -j$(nproc) sockets \
    && apk add --no-cache icu-libs \
    && apk del .build-deps

######################
### Composer image ###
######################
FROM scratch AS composer

# Setup Composer authentetication (https://getcomposer.org/doc/03-cli.md#composer-auth)
ARG COMPOSER_AUTH

ENV COMPOSER_VERSION 2.5.4
RUN set -x \
    && curl --silent --show-error --retry 5 https://getcomposer.org/installer \
        | php -- --install-dir=/usr/local/bin --filename=composer --version=${COMPOSER_VERSION}

CMD ["composer"]

#########################
### Development image ###
#########################
FROM scratch AS development

ARG APP_UID

ENV XDEBUG_VERSION 3.2.0
RUN set -x \
    && apk add --no-cache --virtual .build-deps linux-headers $PHPIZE_DEPS \
    && pecl install xdebug-${XDEBUG_VERSION} \
    && docker-php-ext-enable xdebug \
    && apk del -f .build-deps
COPY php/ /usr/local/etc/php/

RUN cp ${PHP_INI_DIR}/php.ini-development ${PHP_INI_DIR}/php.ini

COPY --from=composer /usr/local/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html

CMD ["php-fpm"]

#####################
### Nginx image #####
#####################
FROM nginx:1.21.6-alpine AS nginx

RUN rm /etc/nginx/conf.d/default.conf

COPY nginx/templates/symfony.conf.template /etc/nginx/templates/

RUN sed -i 's/worker_processes  1/worker_processes  auto/g' /etc/nginx/nginx.conf \
    && sed -i 's/worker_connections  1024/worker_connections  4096;\n    multi_accept on/g' /etc/nginx/nginx.conf \
    && sed -i 's/#tcp_nopush     on/tcp_nopush      on;\n    tcp_nodelay     on;\n    server_tokens   off/g' /etc/nginx/nginx.conf \
    && sed -i 's/#gzip  on/gzip on;\n    gzip_disable  "msie6";\n    gzip_types text\/plain text\/css application\/json text\/javascript application\/javascript text\/xml application\/xml image\/svg+xml/g' /etc/nginx/nginx.conf

RUN sed -i '/default_type/a \\n    map $http_x_request_id $proxied_request_id {\n        default   $http_x_request_id;\n        ""        $request_id;\n    }' /etc/nginx/nginx.conf \
    && sed -i 's/"$http_x_forwarded_for"/"$http_x_forwarded_for" $proxied_request_id/g' /etc/nginx/nginx.conf \
    && echo "fastcgi_param  HTTP_REQUEST_ID    \$proxied_request_id;" >> /etc/nginx/fastcgi_params

ENV SERVER_NAME localhost
ENV FASTCGI_HOST php
ENV FASTCGI_PORT 9000
