FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm \
    sqlite \
    sqlite-dev \
    nginx \
    supervisor \
    libzip-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    bash

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_sqlite pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy dependency files first (for better caching)
COPY composer.json composer.lock package.json package-lock.json ./

# Create necessary directories
RUN mkdir -p bootstrap/cache \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    database

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev --no-interaction --no-scripts

# Install ALL npm dependencies (including dev deps needed for build)
RUN npm ci

# Copy the rest of the application
COPY . .

# Force SQLite configuration by creating .env
RUN echo 'APP_NAME="Murugo Real Estate"' > .env && \
    echo 'APP_ENV=production' >> .env && \
    echo 'APP_KEY=' >> .env && \
    echo 'APP_DEBUG=true' >> .env && \
    echo 'APP_URL=https://murugo.dadishimwe.com' >> .env && \
    echo '' >> .env && \
    echo 'LOG_CHANNEL=stack' >> .env && \
    echo 'LOG_LEVEL=debug' >> .env && \
    echo '' >> .env && \
    echo 'DB_CONNECTION=sqlite' >> .env && \
    echo 'DB_DATABASE=/var/www/database/database.sqlite' >> .env && \
    echo '' >> .env && \
    echo 'BROADCAST_CONNECTION=log' >> .env && \
    echo 'FILESYSTEM_DISK=local' >> .env && \
    echo 'QUEUE_CONNECTION=sync' >> .env && \
    echo 'CACHE_STORE=file' >> .env && \
    echo 'SESSION_DRIVER=file' >> .env && \
    echo 'SESSION_LIFETIME=120' >> .env && \
    echo 'SESSION_DOMAIN=.dadishimwe.com' >> .env && \
    echo 'SESSION_SECURE_COOKIE=true' >> .env && \
    echo 'SESSION_SAME_SITE=lax' >> .env && \
    echo '' >> .env && \
    echo 'MAIL_MAILER=log' >> .env

# Set permissions
RUN touch database/database.sqlite && \
    chown -R www-data:www-data /var/www && \
    chmod -R 775 storage bootstrap/cache database

# Build frontend assets, then remove node_modules to save space
RUN npm run build && rm -rf node_modules

# Optimize autoloader
RUN composer dump-autoload --optimize

# Copy configuration files
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /entrypoint.sh

# Make entrypoint executable
RUN chmod +x /entrypoint.sh

# Expose port
EXPOSE 80

# Use entrypoint script
ENTRYPOINT ["/entrypoint.sh"]
