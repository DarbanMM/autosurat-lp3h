@echo off
REM AutoSurat Docker Startup Script for Windows

echo.
echo ======================================
echo  AutoSurat - Docker Startup
echo ======================================
echo.

REM Check if Docker is installed
docker --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: Docker is not installed or not in PATH
    echo Please install Docker Desktop from: https://www.docker.com/products/docker-desktop
    pause
    exit /b 1
)

echo Checking Docker daemon...
docker ps >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: Docker daemon is not running
    echo Please start Docker Desktop and try again
    pause
    exit /b 1
)

echo ✓ Docker is ready

echo.
echo Starting services...
docker-compose up -d

if %errorlevel% neq 0 (
    echo ERROR: Failed to start services
    pause
    exit /b 1
)

echo.
echo ✓ Services started successfully!
echo.
echo Access the application at:
echo   Frontend: http://localhost
echo   Vite Dev: http://localhost:5173
echo.
echo Database connection:
echo   Host: localhost
echo   Port: 5432
echo   Username: postgres
echo   Password: password
echo.
echo Useful commands:
echo   docker-compose logs -f       View logs
echo   docker-compose down          Stop services
echo   docker-compose exec app bash Open terminal in app container
echo.
pause
