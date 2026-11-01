# syntax=docker/dockerfile:1

# ---------- مرحله ۱: بیلد فرانت (Vite + Tailwind + Basecoat) ----------
FROM node:22-alpine AS frontend
WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

# کل سورس لازم است چون Tailwind کلاس‌ها را از فایل‌های Blade و PHP می‌خواند
COPY . .
RUN npm run build


# ---------- مرحله ۲: وابستگی‌های PHP ----------
FROM composer:2 AS vendor
WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --no-interaction \
    --ignore-platform-reqs

COPY . .
RUN composer dump-autoload --optimize --no-dev --no-scripts


# ---------- مرحله ۳: ایمیج نهایی ----------
# نسخه‌ی PHP را با composer.json پروژه هماهنگ کن (مثلاً 8.2 یا 8.3)
FROM serversideup/php:8.2-fpm-nginx AS final

# این ایمیج روی پورت 8080 گوش می‌دهد (در Coolify هم Ports Exposes = 8080)
ENV PHP_OPCACHE_ENABLE=1 \
    AUTORUN_ENABLED=true \
    AUTORUN_LARAVEL_STORAGE_LINK=true \
    AUTORUN_LARAVEL_MIGRATION=true \
    AUTORUN_LARAVEL_CONFIG_CACHE=true \
    AUTORUN_LARAVEL_ROUTE_CACHE=true \
    AUTORUN_LARAVEL_VIEW_CACHE=true \
    AUTORUN_LARAVEL_EVENT_CACHE=true

WORKDIR /var/www/html

COPY --from=vendor --chown=www-data:www-data /app /var/www/html
COPY --from=frontend --chown=www-data:www-data /app/public/build /var/www/html/public/build

USER www-data
