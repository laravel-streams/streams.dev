# Show Exceptions
set -e

echo "Deploying application..."

(php artisan down || true)
    
    git pull 

    composer install --ignore-platform-reqs --no-dev --no-interaction --prefer-dist --optimize-autoloader

    # php artisan migrate --force

    php artisan queue:restart

    # php artisan optimize

php artisan up

echo "Application deployed."
