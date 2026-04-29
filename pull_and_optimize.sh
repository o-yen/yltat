#!/bin/bash

set -e

cd "$(dirname "$0")"

echo "Pulling latest changes from origin/main..."
git pull origin main

echo "Clearing Laravel caches..."
php artisan optimize:clear

echo "Rebuilding Laravel caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Done."
