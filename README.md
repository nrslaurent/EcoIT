# EcoIT
EcoIT is a symfony application.
## How to install this application
### local installation
#### 1. Requirements
* a web server
* a SQL database
* [PHP](https://www.php.net/)
* [Composer](https://getcomposer.org/)
* [Git](https://git-scm.com/)

We recommand to use [WAMP](https://www.wampserver.com/), [MAMP](https://www.mamp.info/en/mamp/mac/) or LAMP.

### 2. Local installation

- Open a console Terminal
- Go to the directory where you want to install the application
- Run `git clone git@github.com:nrslaurent/EcoIT.git`
- Run your development software (Visual Studio Code, Atom...) and open the downloaded folder
- Copy the ".env" file and rename the new file ".env.local"
- Uncomment the line that match with your database server and complete informations (username, password, database name and, if it's necessary, connection port)
- In your terminal, go into the downloaded folder (run `cd EcoIT`)
- Run `composer install` to install all dependencies
- Finally, run `symfony server:start`
---
Now your need to create database and tables
- In your Terminal, go into project folder and run `php bin/console doctrine:database:create` to create the database
- Run `php bin/console make:migration`to generate migration file
- Finally, run `php bin/console doctrine:migrations:migrate`to create tables in database
---
---

