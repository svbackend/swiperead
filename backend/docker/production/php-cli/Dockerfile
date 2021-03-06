FROM php:7.4-cli-alpine AS builder

ENV BUILD_NO=11111

RUN apk add --no-cache postgresql-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo_pgsql opcache

RUN apk add --no-cache \
    zip unzip libzip-dev php7-zip freetype libpng openssl-dev openssl imap-dev \
    libjpeg-turbo freetype-dev libpng-dev libjpeg-turbo-dev && \
  docker-php-ext-configure gd --with-freetype --with-jpeg && \
  docker-php-ext-configure zip && \
  NPROC=$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) && \
  docker-php-ext-install -j${NPROC} gd && \
  docker-php-ext-install zip && \
  docker-php-ext-configure imap --with-imap --with-imap-ssl && \
  docker-php-ext-install -j${NPROC} imap && \
  apk del --no-cache freetype-dev libpng-dev libjpeg-turbo-dev libzip-dev openssl-dev

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --quiet \
    && rm -rf /root/.composer/cache

WORKDIR /app

COPY ./composer.json ./composer.lock ./

RUN composer install --no-dev --prefer-dist --no-progress --no-suggest --optimize-autoloader \
    && rm -rf /root/.composer/cache

### CLI ###

FROM php:7.4-cli-alpine

ENV BUILD_NO=11111

RUN apk add --no-cache postgresql-dev bash coreutils \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo_pgsql opcache

RUN apk add --no-cache \
    zip unzip libzip-dev php7-zip freetype libpng openssl-dev openssl imap-dev \
    libjpeg-turbo freetype-dev libpng-dev libjpeg-turbo-dev && \
  docker-php-ext-configure gd --with-freetype --with-jpeg && \
  docker-php-ext-configure zip && \
  NPROC=$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) && \
  docker-php-ext-install -j${NPROC} gd && \
  docker-php-ext-install zip && \
  docker-php-ext-configure imap --with-imap --with-imap-ssl && \
  docker-php-ext-install -j${NPROC} imap && \
  apk del --no-cache freetype-dev libpng-dev libjpeg-turbo-dev libzip-dev openssl-dev

RUN mv $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini

COPY ./docker/common/php/conf.d /usr/local/etc/php/conf.d
COPY ./docker/production/php/conf.d /usr/local/etc/php/conf.d

COPY ./docker/common/wait-for-it.sh /usr/local/bin/wait-for-it
RUN chmod 555 /usr/local/bin/wait-for-it

WORKDIR /app

COPY --from=builder /app ./
COPY ./ ./
