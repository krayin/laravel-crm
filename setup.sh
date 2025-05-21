# just to be sure that no traces left
docker-compose down -v

# building and running docker-compose file
docker-compose build && docker-compose up -d

# container id by image name
apache_container_id=$(docker ps -aqf "name=krayin-php-apache")
db_container_id=$(docker ps -aqf "name=krayin-mysql")

# checking connection
echo "Please wait... Waiting for MySQL connection..."
while ! docker exec ${db_container_id} mysql --user=root --password=root -e "SELECT 1" >/dev/null 2>&1; do
    sleep 1
done

# creating empty database for krayin
echo "Creating empty database for krayin..."
while ! docker exec ${db_container_id} mysql --user=root --password=root -e "CREATE DATABASE IF NOT EXISTS krayin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" >/dev/null 2>&1; do
    sleep 1
done

# creating empty database for krayin testing
echo "Creating empty database for krayin testing..."
while ! docker exec ${db_container_id} mysql --user=root --password=root -e "CREATE DATABASE IF NOT EXISTS krayin_testing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" >/dev/null 2>&1; do
    sleep 1
done

# setting up krayin
echo "Now, setting up Krayin..."
docker exec ${apache_container_id} git clone https://github.com/krayin/laravel-crm krayin

# setting krayin stable version
echo "Now, setting up Krayin stable version..."
docker exec -i ${apache_container_id} bash -c "cd krayin && git reset --hard v2.0.1"

# installing composer dependencies inside container
docker exec -i ${apache_container_id} bash -c "cd krayin && composer install"

# moving `.env` file
docker cp .configs/.env ${apache_container_id}:/var/www/html/krayin/.env
docker cp .configs/.env.testing ${apache_container_id}:/var/www/html/krayin/.env.testing

# executing final commands
docker exec -i ${apache_container_id} sh -c "cd krayin && php artisan optimize:clear && php artisan migrate:fresh --seed && php artisan storage:link && php artisan vendor:publish --provider='Webkul\\Core\\Providers\\CoreServiceProvider' --force && php artisan optimize:clear"
