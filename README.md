# dailyTrendApi


Composer is included

```
docker-compose run php-fpm composer 
```

### Run php commands inside container

1 - Go inside a docker dir in project /delivery-food-api/docker

2 - Execute this command
```
docker-compose run php-fpm sh 
``` 
3 - At this momment you can execute php command line inside a container


### Create admin user to manage backend EasyAdmin
1 - First create client_id and client_secret
  ```
 php bin/console fos:oauth-server:create-client --redirect-uri="api/login" --grant-type="password" --grant-type="client_credentials" 
  ``` 
2 - Copy generated values in .env

3 -  Inside a container run next command
 ```
php bin/console fos:user:create adminuser --super-admin 
 ``` 

### Execute Generator Feed Command
1- Inside a container copy and run this commmand
```
php bin/console app:generate:feed
 ``` 
