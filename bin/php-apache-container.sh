# container id
CONTAINER_ID=$(docker ps -aqf "name=krayin-php-apache")

docker exec -w /var/www/html/krayin -it ${CONTAINER_ID} bash
