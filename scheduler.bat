@echo off
cd C:\xampp\htdocs\psm
:loop
php artisan schedule:run
timeout /t 60 /nobreak
goto loop