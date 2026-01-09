#!/bin/bash

echo "=== Creating Minimal Laravel Backend ==="

# Create new Laravel project
echo "📦 Creating Laravel project 'backend'..."
composer create-project laravel/laravel backend

cd backend

# Remove unnecessary files/directories
echo "🗑️  Removing unnecessary files..."
rm -rf tests/
rm -rf storage/framework/testing/
rm -rf resources/js/
rm -rf resources/css/
rm -f vite.config.js
rm -f package.json
rm -f postcss.config.js
rm -f tailwind.config.js
rm -f phpunit.xml

# Minimal .env (keep only essentials)
echo "⚙️  Creating minimal .env..."
cat > .env << 'EOF'
APP_NAME=Backend
APP_ENV=local
APP_KEY=base64:YOUR_KEY_HERE
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=sqlite

CACHE_DRIVER=array
SESSION_DRIVER=array
QUEUE_CONNECTION=sync
EOF

# Create minimal welcome route
echo "🛣️  Creating minimal route..."
cat > routes/web.php << 'EOF'
<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'Hello World';
});
EOF

# Generate app key
echo "🔑 Generating app key..."
php artisan key:generate

# Create SQLite database
echo "💾 Creating SQLite database..."
touch database/database.sqlite

# Clear config cache
echo "🧹 Clearing config cache..."
php artisan config:clear

echo ""
echo "✅ Minimal Laravel backend ready!"
echo ""
echo "To start the server:"
echo "  cd backend"
echo "  php artisan serve"
echo ""
echo "Then visit: http://localhost:8000"