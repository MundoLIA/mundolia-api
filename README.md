<h3>Install</h3>
<ol>
    <li>composer install</li>
    <li>composer dump-autoload</li>
    <li>php artisan migrate:fresh --seed</li>
    <li>php artisan passport:install</li>
    <li>php artisan config:clear</li>
    <li>php artisan queue:listen</li>
    <li>php artisan queue:work --daemon &</li>
</ol>
