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
    nginx \
    supervisor \
    netcat-openbsd \
    && rm -rf /var/lib/apt/lists/*

# 2. Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath

# 3. Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Set working directory
WORKDIR /app

# 5. Copy application files
COPY . .

# 6. BUILD-TIME PERMISSIONS (Base Layer)
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

# 9. Supervisor config (manages PHP-FPM, Nginx, and Reverb)
RUN echo '[supervisord] \n\
nodaemon=true \n\
user=root \n\
logfile=/var/log/supervisor/supervisord.log \n\
pidfile=/var/run/supervisord.pid \n\
\n\
[program:php-fpm] \n\
command=php-fpm --nodaemonize \n\
autostart=true \n\
autorestart=true \n\
stdout_logfile=/dev/stdout \n\
stdout_logfile_maxbytes=0 \n\
stderr_logfile=/dev/stderr \n\
stderr_logfile_maxbytes=0 \n\
\n\
[program:nginx] \n\
command=nginx -g "daemon off;" \n\
autostart=true \n\
autorestart=true \n\
stdout_logfile=/dev/stdout \n\
stdout_logfile_maxbytes=0 \n\
stderr_logfile=/dev/stderr \n\
stderr_logfile_maxbytes=0 \n\
\n\
[program:reverb] \n\
command=/app/artisan-reverb-wrapper.sh \n\
autostart=true \n\
autorestart=true \n\
user=www-data \n\
stdout_logfile=/dev/stdout \n\
stdout_logfile_maxbytes=0 \n\
stderr_logfile=/dev/stderr \n\
stderr_logfile_maxbytes=0 \n\
' > /etc/supervisor/conf.d/supervisord.conf

# 10. Create Reverb startup wrapper script
RUN echo '#!/bin/bash \n\
php /app/artisan reverb:start --host=0.0.0.0 --port=8081 --debug \n\
' > /app/artisan-reverb-wrapper.sh \
    && chmod +x /app/artisan-reverb-wrapper.sh

EXPOSE 80 8081

# 11. ROBUST STARTUP SEQUENCE
CMD ["sh", "-c", "\
    chown -R www-data:www-data /app/storage /app/bootstrap/cache && \
    su -s /bin/sh -c 'php artisan migrate --force && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache' www-data && \
    /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf \
"]