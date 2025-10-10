FROM php:8.1.33-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libpq-dev \
    libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip intl opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configure PHP for development
RUN echo "memory_limit = 512M" > /usr/local/etc/php/conf.d/memory-limit.ini \
    && echo "upload_max_filesize = 50M" >> /usr/local/etc/php/conf.d/memory-limit.ini \
    && echo "post_max_size = 50M" >> /usr/local/etc/php/conf.d/memory-limit.ini

# Install Symfony CLI (optionnel mais utile)
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# Set working directory
WORKDIR /var/www



# Set permissions pour l'utilisateur www-data
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

# Expose port
EXPOSE 9000

CMD ["php-fpm"]