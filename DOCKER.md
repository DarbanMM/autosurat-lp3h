# Running AutoSurat with Docker

This project is now fully containerized. No need for Laragon or local PHP/Node installations!

## Prerequisites

- **Docker** (v20.10+): [Install Docker Desktop](https://www.docker.com/products/docker-desktop)
- **Docker Compose** (included with Docker Desktop)

That's it! No PHP, Node, PostgreSQL, or Composer needed on your machine.

## Quick Start

### 1. Clone/Setup the Project
```bash
cd c:\laragon\www\autosurat-lp3h
```

### 2. Start All Services
```bash
docker-compose up --build
```

The `--build` flag rebuilds the Docker image (first time or after code changes).

### 3. Access the Application

- **Frontend:** http://localhost
- **Hot reload (Vite):** Automatically active at http://localhost:5173
- **Database:** localhost:5432 (PostgreSQL)

## What Each Service Does

| Service | Purpose | URL |
|---------|---------|-----|
| **app** | Laravel PHP-FPM server | Internal (port 9000) |
| **nginx** | Web server & reverse proxy | http://localhost |
| **postgres** | PostgreSQL database | localhost:5432 |
| **node** | Vite dev server for frontend | http://localhost:5173 |

## Common Commands

### Start Services
```bash
docker-compose up
```

### Start in Background
```bash
docker-compose up -d
```

### View Logs
```bash
docker-compose logs -f app          # Laravel logs
docker-compose logs -f postgres      # Database logs
docker-compose logs -f nginx         # Web server logs
docker-compose logs -f node          # Vite dev logs
```

### Stop Services
```bash
docker-compose down
```

### Stop & Delete Data
```bash
docker-compose down -v              # -v removes volumes (database data)
```

### Run Artisan Commands
```bash
docker-compose exec app php artisan migrate
docker-compose exec app php artisan tinker
docker-compose exec app php artisan cache:clear
```

### Run npm Commands
```bash
docker-compose exec node npm install package-name
docker-compose exec node npm run build
```

## Database Access

### From Command Line
```bash
docker-compose exec postgres psql -U postgres -d autosurat
```

### From PostgreSQL GUI Client
- Host: `localhost`
- Port: `5432`
- Username: `postgres`
- Password: `password`
- Database: `autosurat`

## Environment Variables

Edit `.env.docker` to customize:
- `DB_DATABASE`: Database name
- `DB_USERNAME`: Database user
- `DB_PASSWORD`: Database password
- `APP_KEY`: Laravel encryption key (auto-generated)

Then restart:
```bash
docker-compose down && docker-compose up
```

## Troubleshooting

### Database Connection Error
Wait 10-15 seconds after starting Docker for PostgreSQL to be ready.

```bash
docker-compose logs postgres
```

### Port Already in Use
Change ports in `docker-compose.yml`:
```yaml
postgres:
  ports:
    - "5433:5432"  # Change 5433 to available port
```

### Clear Docker Cache
```bash
docker-compose down -v
docker system prune -a
docker-compose up --build
```

### Check Container Status
```bash
docker-compose ps
```

## Production Deployment

For production:
1. Use `.env.production` with secure values
2. Set `APP_DEBUG=false`
3. Set `APP_ENV=production`
4. Use proper SSL certificates with Nginx
5. Use a managed database service

```bash
docker-compose -f docker-compose.yml -f docker-compose.prod.yml up
```

## Tips

- **Live reload:** Frontend changes auto-reload via Vite
- **Database migrations:** Run automatically on startup
- **Code changes:** No rebuild needed for PHP/Vue changes
- **Node packages:** Install with `docker-compose exec node npm install`
- **Composer packages:** Install with `docker-compose exec app composer require package`

Enjoy your Dockerized Laravel development! 🐳
