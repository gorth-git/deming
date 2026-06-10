#!/bin/bash

echo "Initialize database"

# Mandatory to force the database migrations
APP_ENV="local"

# Migarte the database
php artisan migrate

# Create the admin user if it does not exist
php artisan db:seed --class=DatabaseSeeder
