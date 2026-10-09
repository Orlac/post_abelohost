FROM php:fpm

# Системные пакеты и PHP-расширения
RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip zip curl nano libzip-dev \
    && docker-php-ext-install pdo_mysql mysqli zip \
    && rm -rf /var/lib/apt/lists/*

# Composer (latest)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Node.js + npm (latest)
COPY --from=node:current-slim /usr/local/bin/node /usr/local/bin/node
COPY --from=node:current-slim /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx

# Обычный пользователь (UID/GID 1000 — как у первого пользователя в большинстве Linux/WSL)
RUN groupadd -g 1000 app \
    && useradd -m -u 1000 -g 1000 -s /bin/bash app

# Скрипт автоустановки composer/npm пакетов при старте
COPY --chmod=755 docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh

WORKDIR /var/www/html

# Всё (php-fpm, docker exec, composer, npm) работает не под root
USER app

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]