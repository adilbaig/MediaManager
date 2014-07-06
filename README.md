My Music Manager
========================

Installation
----

This bundle requires PHP 5.3 only.

Install the vendor libraries by running. When asked for 'upload_dir' parameter, give
the full path to `Symfony/web/uploads` dir

````
cd Symfony
php composer.phar install
````

Sample Apache VHost conf file
----

Copy the following snippet to your Apache vhosts file.

````
<VirtualHost *:8080>

    #ServerName vagrant.mediaplayer

    #ADJUST PER YOUR SYSTEM
    DocumentRoot /vagrant_src/netbeans/mediaplayer/Symfony/web
    <Directory /vagrant_src/netbeans/mediaplayer/Symfony/web>
        Order allow,deny
        Allow from all
        AllowOverride All
        Options -Indexes
        Require all granted
    </Directory>
</VirtualHost>
````

Restart Apache after you're done

Setup MySQL
----
Create the Mysql database as follows. 

````
mysql -u root -p < generate-schema.sql
````

This will also import users.

Run
----

Go to `localhost:8080`


Run Tests
----

````
cd Symfony
php phpunit.phar -c app/phpunit.xml.dist
````
