release: php artisan migrate --seed --force && npm install && npm run build
web: vendor/bin/heroku-php-apache2 public/
worker: php artisan queue:listen --tries=3 --timeout=1800
