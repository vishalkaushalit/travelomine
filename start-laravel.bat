@echo off
cd /d "C:\Program Files\Ampps\www\flight_crm\flight_crm"
echo Starting Laravel server at http://192.168.1.5:8000 
echo Press Ctrl+C to stop the server
echo.
php artisan serve --host=192.168.1.5 --port=8000
pause
