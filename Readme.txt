[Symfony 4 install]

$ composer create-project symfony/skeleton symfony


[create .htaccess]
$ cd symfony
$ composer require symfony/apache-pack


Edit in symfony/config/packages/doctrine.yaml
Line:5 	server_version: '5.7' (**  should match your mysql version in docker-compose.yml)

Edit in symfony/.env
line:27 		DATABASE_URL=mysql://<<user_name>>:<<password>>@<<docker_service>>:<<port>>/
<<database_name>>
Example	DATABASE_URL=mysql://root:root@mysql:3306/db

Inside Docker machine (/var/www/html)
$ php bin/console doctrine:database:create  (**  That create database)

[Xdebug config in Phpstorm IDE]

url: https://gist.github.com/chadrien/c90927ec2d160ffea9c4

PHPStorm IDE go to: Languages & Frameworks > PHP > Debug > DBGp Proxy and set the following settings:
Host: your IP address  (get from host machine -** $ ifconfig)
Port: 9000
