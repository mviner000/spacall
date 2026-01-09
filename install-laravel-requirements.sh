#!/bin/bash

echo "=== Installing Laravel Requirements on Ubuntu 24 ==="

# Update package list
echo "📦 Updating package list..."
sudo apt update

# Install PHP 8.2+ and required extensions
echo "🐘 Installing PHP and extensions..."
sudo apt install -y php8.3 php8.3-cli php8.3-common php8.3-mysql php8.3-zip php8.3-gd php8.3-mbstring php8.3-curl php8.3-xml php8.3-bcmath php8.3-sqlite3

# Verify PHP installation
echo "✅ PHP version:"
php -v

# Install Composer
echo "🎵 Installing Composer..."
cd ~
curl -sS https://getcomposer.org/installer -o composer-setup.php
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
rm composer-setup.php

# Verify Composer installation
echo "✅ Composer version:"
composer --version

# Optional: Install Node.js (if you need frontend tools)
echo "📦 Installing Node.js (optional)..."
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Verify Node.js installation
echo "✅ Node.js version:"
node -v
npm -v

# Optional: Install SQLite
echo "💾 Installing SQLite..."
sudo apt install -y sqlite3
