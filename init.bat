@echo off
rem ============================================================
rem architect-ai — Fresh Clone Setup (Windows)
rem Run this once after cloning; it's idempotent — safe to re-run.
rem ============================================================
setlocal

set "PROJECT_DIR=%~dp0"
title architect-ai setup

echo.
echo  [1/4] Installing PHP dependencies...
call composer install --no-interaction
if %ERRORLEVEL% neq 0 (
    echo [FAIL] composer install failed.
    pause
    exit /b 1
)

echo.
echo  [2/4] Running database migrations (SQLite auto-created by bootstrap/app.php)...
php "%PROJECT_DIR%artisan" migrate --force
if %ERRORLEVEL% neq 0 (
    echo [FAIL] migrations failed.
    pause
    exit /b 1
)

echo.
echo  [3/4] Seeding dev user...
php "%PROJECT_DIR%artisan" db:seed --class=DevUserSeeder --force
echo.
echo  [4/4] Starting Laravel dev server on http://localhost:8081 ...
echo  Dev credentials: admin@dev.local / password123
echo.
start "" "http://localhost:8081"
php "%PROJECT_DIR%artisan" serve --port=8081
