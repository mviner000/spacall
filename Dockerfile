FROM php:8.2-fpm

# 1. Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpq-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx

# 2. Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath

# 3. Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Set working directory
WORKDIR /app

# 5. Copy application files
COPY . .

# 6. FIX PERMISSIONS (Critical for Newbies)
# This creates the necessary folders and gives the web user (www-data) 
# permission to write logs and cache files.
RUN mkdir -p storage/logs storage/framework/sessions storage/framework/views storage/framework/cache bootstrap/cache \
    && chown -R www-data:www-data /app \
    && chmod -R 775 storage bootstrap/cache

# 7. Install dependencies
RUN composer install --no-dev --optimize-autoloader

# 8. Nginx config
RUN echo 'server { \n\
    listen 80; \n\
    root /app/public; \n\
    index index.php; \n\
    \n\
    access_log /dev/stdout; \n\
    error_log /dev/stderr; \n\
    \n\
    location / { \n\
        try_files $uri $uri/ /index.php?$query_string; \n\
    } \n\
    \n\
    location ~ \.php$ { \n\
        fastcgi_pass 127.0.0.1:9000; \n\
        fastcgi_index index.php; \n\
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name; \n\
        include fastcgi_params; \n\
    } \n\
}' > /etc/nginx/sites-available/default

EXPOSE 80

# 9. STARTUP SEQUENCE
# We use "su -s /bin/sh -c ... www-data" to run migrations and cache commands 
# as the web user instead of root. This prevents permission errors.
CMD ["sh", "-c", "su -s /bin/sh -c 'php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache' www-data && php-fpm -D && nginx -g 'daemon off;'"]