FROM php:8.4-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd xml \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy composer files first for better Docker layer caching
COPY composer.json composer.lock ./

# Install dependencies (no dev)
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Copy the rest of the application
COPY . .

# Run post-install scripts
RUN composer run-script post-autoload-dump 2>/dev/null || true

# Cache config, routes, views
RUN php artisan config:clear \
    && php artisan route:clear \
    && php artisan view:clear

# Create storage directories
RUN mkdir -p storage/logs \
    && mkdir -p storage/framework/sessions \
    && mkdir -p storage/framework/views \
    && mkdir -p storage/framework/cache \
    && chmod -R 775 storage bootstrap/cache

# Expose port (Railway sets PORT env var)
EXPOSE ${PORT:-8080}

# Start command: run migrations then serve
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
