#!/bin/bash

# Create Symfony project if it doesn't exist
if [ ! -f "composer.json" ]; then
    docker-compose run --rm php composer create-project symfony/website-skeleton .
fi

# Build and start Docker containers
docker-compose up -d --build

# Install Composer dependencies
docker-compose exec php composer install

# Install npm dependencies
docker-compose exec php yarn install

# Build assets
docker-compose exec php yarn build

# Clear cache and fix permissions
docker-compose exec php php bin/console cache:clear

# Create required directories
docker-compose exec php mkdir -p public/build
docker-compose exec php mkdir -p templates
docker-compose exec php mkdir -p src/Controller
docker-compose exec php mkdir -p src/Service

# Ensure proper permissions
docker-compose exec php chmod -R 777 var
docker-compose exec php chmod -R 777 public/build

# Clear cache
docker-compose exec php php bin/console cache:clear

echo "Setup complete! Access the application at http://localhost:8080"