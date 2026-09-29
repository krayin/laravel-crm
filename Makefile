# ==============================================
# Krayin CRM - Makefile for Docker
# ==============================================

.PHONY: help build up down logs stop clean reseed key

help:
	@echo "Krayin CRM - Docker Commands:"
	@echo "  make build     - Build Docker images"
	@echo "  make up       - Start all services"
	@echo "  make down    - Stop all services"
	@echo "  make logs    - View logs"
	@echo "  make stop    - Stop containers"
	@echo "  make clean   - Remove containers and volumes"
	@echo "  make reseed  - Reseed database"
	@echo "  make key     - Generate APP_KEY"

# Generate APP_KEY
key:
	docker-compose run --rm app php artisan key:generate --show

# Build images
build:
	docker-compose build --no-cache

# Start services
up:
	docker-compose up -d
	@echo ">>> Waiting for services to be ready..."
	@sleep 5
	@echo ">>> Services started!"
	@echo ">>> App: http://localhost:8080"

# Stop services
down:
	docker-compose down

# View logs
logs:
	docker-compose logs -f

# Stop containers
stop:
	docker-compose stop

# Clean up (remove containers + volumes)
clean:
	docker-compose down -v

# Reseed database
reseed:
	docker-compose exec app php artisan migrate:fresh --seed