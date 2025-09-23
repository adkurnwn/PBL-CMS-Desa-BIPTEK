FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libgd-dev \
    jpegoptim \
    optipng \
    pngquant \
    gifsicle \
    vim \
    nodejs \
    npm \
    supervisor

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy composer files first for better Docker layer caching
COPY composer.json composer.lock /var/www/
RUN composer install --no-dev --no-scripts --no-autoloader --optimize-autoloader

# Copy package.json for Node dependencies
COPY package.json package-lock.json* /var/www/

# Copy application code
COPY . /var/www

# Create production environment files
COPY .env.example /var/www/.env

# Complete composer installation and generate autoload
RUN composer dump-autoload --optimize

# Install ALL npm dependencies (including dev dependencies for build)
RUN npm install

# Build production assets
RUN npm run build

# Remove dev dependencies after build to reduce image size
RUN npm prune --production

# Generate Laravel application key and run optimizations
RUN php artisan key:generate --force \
    && php artisan config:cache 

# Create a backup of public files for volume initialization
RUN cp -r /var/www/public /var/www/public_backup

# Set proper permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache \
    && chmod -R 755 /var/www/public \
    && chmod 775 .env

# Create supervisor configuration for Laravel processes
RUN mkdir -p /var/log/supervisor
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Create entrypoint script to handle volume initialization
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Expose port 9000 and start php-fpm server
EXPOSE 9000
ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm"]