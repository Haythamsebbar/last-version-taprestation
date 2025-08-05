# Use PHP 8.2 with Apache
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies in one layer with cleanup
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    default-mysql-client \
    && docker-php-ext-install -j$(nproc) pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Node.js 18 (faster than system nodejs)
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy Apache configuration
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Copy composer files first for better caching
COPY composer.json ./
COPY composer*.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader

# Copy package files for Node dependencies
COPY package*.json ./
RUN npm install

# Copy application files (excluding vendor and node_modules)
COPY --chown=www-data:www-data . .

# Complete composer installation
RUN composer dump-autoload --optimize

# Build frontend assets
RUN npm run build

# Clean up dev dependencies after build
RUN npm prune --omit=dev

# Create and set permissions for specific directories only
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Copy startup script
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Expose port 80
EXPOSE 80

# Start the application
CMD ["/usr/local/bin/start.sh"]