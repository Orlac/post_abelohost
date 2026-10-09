#!/bin/bash
set -e

cd /var/www/html

# Composer: ставим пакеты, если есть composer.json
if [ -f composer.json ]; then
    echo "[entrypoint] composer.json найден — composer install"
    composer install --no-interaction --prefer-dist \
        || echo "[entrypoint] ВНИМАНИЕ: composer install завершился с ошибкой"
else
    echo "[entrypoint] composer.json не найден — пропускаю composer install"
fi

# npm: ставим пакеты, если есть package.json
if [ -f package.json ]; then
    echo "[entrypoint] package.json найден — npm install"
    npm install \
        || echo "[entrypoint] ВНИМАНИЕ: npm install завершился с ошибкой"
else
    echo "[entrypoint] package.json не найден — пропускаю npm install"
fi

exec "$@"
