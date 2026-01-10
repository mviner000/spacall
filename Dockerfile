FROM ghcr.io/railwayapp/nixpacks:ubuntu-1745885067

WORKDIR /app

# Install PHP, Composer, Node.js, and Nginx
RUN nix-env -iA nixpkgs.php82 \
    nixpkgs.php82Packages.composer \
    nixpkgs.nodejs_20 \
    nixpkgs.nginx \
    nixpkgs.php82Extensions.pgsql \
    nixpkgs.php82Extensions.pdo_pgsql \
    && nix-collect-garbage -d

# Copy application files
COPY . .

# Install dependencies
RUN composer install --ignore-platform-reqs --no-dev --optimize-autoloader && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Create nginx config
RUN echo 'server { \n\
    listen 80; \n\
    root /app/public; \n\
    index index.php; \n\
    \n\
    location / { \n\
        try_files $uri $uri/ /index.php?$query_string; \n\
    } \n\
    \n\
    location ~ \.php$ { \n\
        fastcgi_pass 127.0.0.1:9000; \n\
        fastcgi_index index.php; \n\
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; \n\
        include /nix/store/*/conf/fastcgi_params; \n\
    } \n\
}' > /etc/nginx/nginx.conf

EXPOSE 80

CMD php-fpm -D && nginx -g 'daemon off;'