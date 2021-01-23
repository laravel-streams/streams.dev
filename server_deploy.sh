#!/bin/sh
set -e

echo "Deploying application..."

# Enter maintenance mode
(php artisan down || true)
    
    # Update codebase
    git checkout production
    git pull origin/production

    # Install dependencies based on lock file
    composer install --no-interaction --prefer-dist --optimize-autoloader

    # Migrate database
    # php artisan migrate --force

    # Restart queue workers
    php artisan queue:restart

    # Clear cache/optimize
    php artisan optimize

    # Reload PHP to update opcache?
    # echo "" | sudo -S service php74 reload
    
# Exit maintenance mode
php artisan up

echo "Application deployed!"
