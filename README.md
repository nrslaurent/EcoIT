# EcoIT

EcoIT is a symfony application.

## How to install this application

### 1. local installation

#### Requirements

- a web server
- a SQL database
- [PHP](https://www.php.net/)
- [Composer](https://getcomposer.org/)
- [Git](https://git-scm.com/)

I recommand to use [WAMP](https://www.wampserver.com/), [MAMP](https://www.mamp.info/en/mamp/mac/) or LAMP.

### Installation

- Open a console terminal
- Go to the directory where you want to install the application
- Run `git clone git@github.com:nrslaurent/EcoIT.git`
- Run your development software (Visual Studio Code, Atom...) and open the downloaded folder
- Copy the ".env" file and rename the new file ".env.local"
- Uncomment the line that match with your database server and complete informations (username, password, database name and, if it's necessary, connection port)
- In your terminal, go into the downloaded folder (run `cd EcoIT`)
- Run `composer install` to install all dependencies
- Finally, run `symfony server:start`

Now you need to create database and tables:

- In your terminal, go into project folder and run `php bin/console doctrine:database:create` to create the database
- Run `php bin/console make:migration`to generate migration file
- Finally, run `php bin/console doctrine:migrations:migrate`to create tables in database

Add an administrator account

- In your terminal, go into project folder and run `php bin/console security:hash-password` to generate a hashed password (choose App\Entity\User at the next step et enter the password)
- Launch your SGDB manager application (ex: PhpMyAdmin) and enter SQL request following (replace password by hashed password you got previously): `INSERT INTO user(email, roles, password,is_validated) VALUES ('admin@test.fr','["ROLE_USER","ROLE_ADMIN"]','password',true)`

---

---

---

### 2. Deployment on web

We will use Heroku to host this application, you can reach documentation [here](https://devcenter.heroku.com/articles/deploying-symfony4)

#### Requirements

- Heroku user account
- [Heroku CLI](https://devcenter.heroku.com/articles/getting-started-with-php#set-up)
- [Git](https://git-scm.com/)

### Installation

- Open a terminal
- Go to the directory where you want to install the application
- Run `git clone git@github.com:nrslaurent/EcoIT.git`
- In your terminal, go into the downloaded folder (run `cd EcoIT`)
- Run `heroku login`
- Run `heroku create` to create a Heroku application
- Run these commands in your terminal to create a "procfile":
  - `echo 'web: heroku-php-apache2 public/' > Procfile`
  - `git add Procfile`
  - `git commit -m "Heroku Procfile"`
- Run `heroku config:set APP_ENV=prod` to set prod environment
- Finally, run `git push heroku master` to deploy on Heroku

I recommand you to activate [secure access](https://devcenter.heroku.com/articles/automated-certificate-management) but it's not free.

**_To access any URL on your application you have to create htaccess file. In your terminal, run:_**

- `composer require symfony/apache-pack`
- `git add composer.json composer.lock symfony.lock public/.htaccess`
- `git commit -m "apache-pack"`

**_Add buildPack for Webpack_**

Run in your terminal:

- `heroku buildpacks:add heroku/php`
- `heroku buildpacks:add --index 1 heroku/nodejs`

**_Database: I recommand [JawsDB MySQL](https://devcenter.heroku.com/articles/jawsdb)_**

- Run `heroku addons:create jawsdb` in your terminal (into project folder) to install JawsDB MySQL
- Run `heroku config:get JAWSDB_URL`to get database informations
- Run `heroku config:set DATABASE_URL=mysql://username:password@hostname:port/default_schema` (replace "mysql..." by what you got previously)
- Run `heroku run php bin/console doctrine:migrations:migrate`to create tables in database
