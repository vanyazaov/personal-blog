FROM php:8.3-fpm-bookworm

ARG UID=1000
ARG GID=1000
ARG USERNAME=app

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        curl \
        unzip \
        ca-certificates \
        libzip-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libonig-dev \
        libxml2-dev \
        libicu-dev \
        default-mysql-client \
        $PHPIZE_DEPS \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        mysqli \
        mbstring \
        zip \
        exif \
        pcntl \
        bcmath \
        gd \
        intl \
        opcache \
    && pecl install redis xdebug \
    && docker-php-ext-enable redis xdebug \
    && apt-get purge -y --auto-remove $PHPIZE_DEPS \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN if getent group ${GID} >/dev/null; then \
        groupmod -n ${USERNAME} "$(getent group ${GID} | cut -d: -f1)"; \
    else \
        groupadd -g ${GID} ${USERNAME}; \
    fi \
 && if id -u ${UID} >/dev/null 2>&1; then \
        usermod -l ${USERNAME} -d /home/${USERNAME} -m -g ${GID} "$(id -un ${UID})"; \
    else \
        useradd -u ${UID} -g ${GID} -m -s /bin/bash ${USERNAME}; \
    fi \
 && mkdir -p /var/www /home/${USERNAME}/.composer \
 && chown -R ${UID}:${GID} /var/www /home/${USERNAME}

WORKDIR /var/www

USER ${UID}:${GID}

CMD ["php-fpm"]
