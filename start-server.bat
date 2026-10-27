@echo off
title Ascend — Laravel Midterm Server (http://127.0.0.1:8000)
set "PATH=%LOCALAPPDATA%\Microsoft\WinGet\Packages\PHP.PHP.8.3_Microsoft.Winget.Source_8wekyb3d8bbwe;%PATH%"
cd /d "%~dp0"
echo Starting Ascend Laravel Server at http://127.0.0.1:8000 ...
start "" "http://127.0.0.1:8000"
php artisan serve --host=127.0.0.1 --port=8000
pause
