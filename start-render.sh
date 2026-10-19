
#!/bin/sh
set -e

cd /var/www/html

php artisan migrate --force

exec /start.sh
