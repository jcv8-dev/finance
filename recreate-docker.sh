docker stop finance
docker rm finance
docker build --network=host -t finance .
docker run -d --name finance --net webhosting -p 8081:80 --restart unless-stopped finance
