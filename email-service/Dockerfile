FROM php:8.5-cli-alpine3.23 AS local

ENV TZ=UTC
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_HOME=/.composer
RUN mkdir /.composer && chmod a+rwx /.composer -R
RUN cp $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
COPY --from=composer/composer:2-bin /composer /usr/bin/composer

RUN install-php-extensions pdo_pgsql

RUN apk update \
    && apk --no-cache add git zip vim bash

ARG APP_UID=1000
ARG APP_USER=appuser
RUN adduser -D -u ${APP_UID} ${APP_USER}
USER ${APP_USER}
WORKDIR "/app"
