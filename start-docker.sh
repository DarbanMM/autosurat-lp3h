#!/bin/bash

# AutoSurat Docker Startup Script

echo ""
echo "======================================"
echo "  AutoSurat - Docker Startup"
echo "======================================"
echo ""

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "ERROR: Docker is not installed"
    echo "Please install Docker from: https://www.docker.com/products/docker-desktop"
    exit 1
fi

echo "✓ Docker found"

# Check if Docker daemon is running
if ! docker ps &> /dev/null; then
    echo "ERROR: Docker daemon is not running"
    echo "Please start Docker and try again"
    exit 1
fi

echo "✓ Docker daemon is running"

echo ""
echo "Starting services..."
docker-compose up -d

if [ $? -ne 0 ]; then
    echo "ERROR: Failed to start services"
    exit 1
fi

echo ""
echo "✓ Services started successfully!"
echo ""
echo "Access the application at:"
echo "  Frontend: http://localhost"
echo "  Vite Dev: http://localhost:5173"
echo ""
echo "Database connection:"
echo "  Host: localhost"
echo "  Port: 5432"
echo "  Username: postgres"
echo "  Password: password"
echo ""
echo "Useful commands:"
echo "  docker-compose logs -f       View logs"
echo "  docker-compose down          Stop services"
echo "  docker-compose exec app bash Open terminal in app container"
echo ""
