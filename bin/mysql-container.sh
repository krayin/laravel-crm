# container id
CONTAINER_ID=$(docker ps -aqf "name=krayin-mysql")

docker exec -it ${CONTAINER_ID} bash
