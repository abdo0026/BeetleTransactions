composer install
php artisan queue:table
php artisan migrate
php artisan passport:install
php artisan passport:keys
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan config:clear
php artisan migrate
php artisan db:seed


pleas do not forget to run laravel queue