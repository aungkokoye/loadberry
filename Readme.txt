Assume Apache web server is running with MySql 8.0

Move that file to site-available for Apache config
vhost->000-default.conf

Here is MySql connection string
In /var/www/html/symfony/.env
line:27 DATABASE_URL=mysql://<<user_name>>:<<password>>@<<docker_service>>:<<port>>/<<database_name>>
Example	DATABASE_URL=mysql://root:root@mysql:3306/db

$ cd /var/www/html
$ php bin/console doctrine:database:create  (**  That create database)
$ php bin/console doctrine:migrations:migrate  ( ** run migration)

Create New User
$ cd /var/www/html
$ docker exec -it webapp bash
$ php bin/console app:create-user <email> <password> (** email is acts like username)

Change password
$ cd /var/www/html
$ php bin/console app:update-pass <email> <password>

Composer Update
$ cd symfony
$ composer update

Create folder for file upload
$ cd /var/www/html/symfony/files
$ mkdir files

File Permission
Make sure web-sever can readable, writeable and executable on
/var/www/html/symfony/files where all uploded files will be stored.

User can upload the files in index page. Max file size is 210 MB.

If user wants uploaded file list view, click cogs icon form upper right conner.
User must login to view that page. Note that if user makes more than 3 failure login Attempts, need to wait 5 mins
to login again.

======================================================================================================================

For Local ENV with docker compose
Need to install docker compose on local machine.

Local Machine
$ cd <project root>
$ docker-compose up --build -d

$ cd symfony
$ composer update

Here is MySql connection string
In /var/www/html/symfony/.env
line:27 DATABASE_URL=mysql://<<user_name>>:<<password>>@<<docker_service>>:<<port>>/<<database_name>>
Example	DATABASE_URL=mysql://root:root@mysql:3306/db

$ docker exec -it webapp bash
$ php bin/console doctrine:database:create  (**  That create database)
$ php bin/console doctrine:migrations:migrate  ( ** run migration)

Create New User
$ php bin/console app:create-user <email> <password> (** email is acts like username)

Change password
$ php bin/console app:update-pass <email> <password>

Create folder for file upload
$ docker exec -it webapp bash
$ mkdir files

File Permission
Make sure docker can readable, writeable and executable on
symfony/files where all uploded files will be stored.
$ chmod 774 files
$ chown files www-data:www-data


Website Address for local ENV
localhost:8750

MySql port for local ENV
3400

User can upload the files in index page. Max file size is 210 MB.

If user wants uploaded file list view, click cogs icon form upper right conner.
User must login to view that page. Note that if user makes more than 3 failure login Attempts, need to wait 5 mins
to login again.





