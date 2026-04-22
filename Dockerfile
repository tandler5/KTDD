# Stage 1: Build PHP dependencies
FROM composer:2.7 as vendor
WORKDIR /app
COPY composer.json composer.lock ./
# Install only production dependencies
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy the rest of the application
COPY . .

# Delete any existing bootstrap/cache files that might have been copied
# and were generated on the host with dev dependencies
RUN rm -rf bootstrap/cache/*.php

# Now generate optimized autoload and run post-autoload scripts
RUN composer dump-autoload --no-dev --optimize

# Stage 2: Final image
FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    oniguruma-dev \
    icu-dev

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath intl

# Configure non-root user
RUN addgroup -g 1000 www && adduser -u 1000 -G www -s /bin/sh -D www

# Set working directory
WORKDIR /var/www/html

# Copy application code from builder
COPY --from=vendor --chown=www:www /app /var/www/html

# Permissions for Laravel
RUN chown -R www:www /var/www/html/storage /var/www/html/bootstrap/cache

# Switch to non-root user
USER www

# Expose port 9000
EXPOSE 9000

# Healthcheck
HEALTHCHECK --interval=30s --timeout=3s \
  CMD php-fpm -t || exit 1

CMD ["php-fpm"]
