docker stop finance
docker rm finance
docker build --network=host -t jcv8/finance:0.1 .
docker run -d --name finance --net webhosting -p 8081:80 --restart unless-stopped finance
