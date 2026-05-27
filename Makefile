.PHONY: help up down logs bash artisan npm build clean

help:
	@echo "AutoSurat Docker Commands"
	@echo ""
	@echo "Usage:"
	@echo "  make up              Start all services"
	@echo "  make down            Stop all services"
	@echo "  make logs            View all logs (follow)"
	@echo "  make bash            Open bash in app container"
	@echo "  make artisan CMD=... Run artisan command"
	@echo "  make npm CMD=...     Run npm command"
	@echo "  make migrate         Run database migrations"
	@echo "  make fresh           Full reset (delete data)"
	@echo "  make build           Build production assets"
	@echo "  make clean           Stop & remove everything"

up:
	docker-compose up -d
	@echo "✓ Services started!"
	@echo "  Frontend: http://localhost"
	@echo "  Vite: http://localhost:5173"

down:
	docker-compose down
	@echo "✓ Services stopped"

logs:
	docker-compose logs -f

bash:
	docker-compose exec app bash

artisan:
	docker-compose exec app php artisan $(CMD)

npm:
	docker-compose exec node npm $(CMD)

migrate:
	docker-compose exec app php artisan migrate

fresh:
	docker-compose down -v
	docker-compose up --build -d
	@echo "✓ Fresh start complete!"

build:
	docker-compose exec node npm run build

clean:
	docker-compose down -v
	docker system prune -a
	@echo "✓ Everything cleaned!"

ps:
	docker-compose ps

db:
	docker-compose exec postgres psql -U postgres -d autosurat
