@echo off
cd /d C:\laragon\www\Ngocdung
php artisan queue:work
pause