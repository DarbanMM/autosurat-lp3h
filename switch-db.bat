@echo off
REM AutoSurat Database Switcher - Windows

echo.
echo ====================================
echo  AutoSurat Database Switcher
echo ====================================
echo.

if "%1"=="" (
    echo Usage: switch-db.bat [option]
    echo.
    echo Options:
    echo   local       Use local Docker PostgreSQL
    echo   supabase    Use Supabase PostgreSQL
    echo.
    exit /b 1
)

if "%1"=="local" (
    echo Switching to LOCAL database...
    copy .env.local .env
    copy docker-compose.local.yml docker-compose.yml
    echo.
    echo ✓ Switched to LOCAL database!
    echo.
    echo To start: docker-compose up -d
    echo.
) else if "%1"=="supabase" (
    echo Switching to SUPABASE database...
    copy .env.supabase .env
    copy docker-compose.supabase.yml docker-compose.yml
    echo.
    echo ✓ Switched to SUPABASE database!
    echo.
    echo To start: docker-compose up -d
    echo.
) else (
    echo Error: Invalid option "%1"
    echo.
    echo Usage: switch-db.bat [local^|supabase]
    exit /b 1
)
