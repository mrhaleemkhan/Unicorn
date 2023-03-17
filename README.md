Unicorn (Symfony 5.4)
========================


Requirements
------------
* PHP 7.2 or later
* Composer
* PostgreSQL



Installation
--------------
* Clone the repository: <br>
<code>git clone https://github.com/mrhaleemkhan/Unicorn.git</code><br>

* Install required packages: <br>
<code>composer install -vvv</code><br>

* Create a new database and update the .env file with your database credentials: <br>
<code>DATABASE_URL="postgresql://username:password@127.0.0.1:5432/unicorn?serverVersion=15&charset=utf8"</code><br>



Usage
-----

<small>Development</small>
* Create a new database: <br>
<code>php bin/console doctrine:database:create</code><br>
* Update doctrine schema<br>
<code>php bin/console doctrine:schema:update --force</code><br>
* To run the application locally, start the built-in web server <br>
<code>php bin/console server:start</code><br>
<small>Then, visit http://localhost:8000 in your web browser to see the application.</small>

Test
----
* Create a new test database: <br>
<code>php bin/console doctrine:database:create --env=test</code><br>
* Update doctrine schema<br>
<code>php bin/console doctrine:schema:update --force --env=test</code><br>
* Run phpunit tests <br>
<code>./bin/phpunit</code><br>


