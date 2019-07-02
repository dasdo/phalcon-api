# phalcon-api
Baka API using Phalcon

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bakaphp/phalcon-api/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/bakaphp/phalcon-api/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/bakaphp/phalcon-api/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/bakaphp/phalcon-api/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/bakaphp/phalcon-api/badges/build.png?b=master)](https://scrutinizer-ci.com/g/bakaphp/phalcon-api/build-status/master)


Implementation of an API application using the Phalcon Framework [https://phalconphp.com](https://phalconphp.com)

### Installation
- Clone the project
- Copy `storage/ci/.env.example` and paste it in the root of the project and rename it `.env`
- On `phalcon-api/.env` in `MYSQL_ROOT_PASSWORD` and `DATA_API_MYSQL_PASS` assign the root password for MySQL.
- On `phalcon-api/.env`, update MySQL credentials (`DATA_API_MYSQL_NAME,DATA_API_MYSQL_USER,DATA_API_MYSQL_PASS`)
- On `phalcon-api/.env`, change `DATA_API_MYSQL_HOST =  localhost` to `DATA_API_MYSQL_HOST =  mysql`
- Download [Canvas Core](https://github.com/bakaphp/canvas-core) and copy it on the same folder where `phalcon-api` is located(Both projects must be in the same folder).
- On  `phalcon-api/library/Core/autoload.php` comment `require dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . '/vendor/canvas/core/src/Core/functions.php';` and uncomment `require  '/         canvas-core/src/Core/functions.php';`
- On `phalcon-api/library/Core/autoload.php` uncomment `'Canvas' => '/canvas-core/src',`
- Run Docker containers with the `docker-compose up --build` command
- After the build, access the project main container with `docker exec -it id_of_docker_container sh`
- Inside the container's console run get inside the `apps` folder, `cd app/`
- Inside the container's console run  `./vendor/bin/phinx migrate -e production` to create the db , you need to have the phinx.php file , if you dont see it on your main filder you can       find the copy at `storage/ci/phinx.php`
- Inside the container's console run `./vendor/bin/phinx seed:run` to create the necesary initial data
- Inside the container's console run `php cli/cli.php acl` AND `php cli/cli.php acl crm` to create the default roles of the system
- Inside the container's console run `./vendor/bin/codecept run` to run project tests.

**NOTE** : This requires [docker](https://www.docker.com/) to be present in your system. Visit their site for installation instructions.

**NOTE** : To ensure the project runs smoothly in a development environment you must comment or remove `canvas/core": "dev-master"` dependency from composer.json

### CLI
- On every deploy crear the session caches `./app/php cli/cli.php clearcache` 
- On every deploy update your DB `./app/vendor/bin/phinx migrate -e production`
- Queue to clear jwt sessions `./app/php cli/cli.php clearcache sessions`

### Features
- User Managament
  - Registration , Login, Multi Tenant 
- ACL *working on it
- Saas Configuracion *working on it
 - Company Configuration
 - Payment / Free trial flow
- Rapid API CRUD Creation

##### JWT Tokens
As part of the security of the API, [JWT](https://jwt.io) are used. JSON Web Tokens offer an easy way for a consumer of the API to send requests without the need to authenticate all the time. The expiry of each token depends on the setup of the API. An admin can easily keep the expiry very short, thus consumers will always have to log in first and then access a resource, or they can increase the "life" of each token, thus having less calls to the API.

##### Middleware
- Lazy loading to save resources per request
- Stop execution as early as possible when an error occurs
- Execution
    - NotFound          - 404 when the resource requested is not found
    - Authentication    - After a `/login` checks the `Authentication` header
    - TokenUser         - When a token is supplied, check if it corresponds to a user in the database
    - TokenVerification - When a token is supplied, check if it is correctly signed
    - TokenValidation   - When a token is supplied, check if it is valid (`issuedAt`, `notBefore`, `expires`) 

##### Baka HTTP
We use the library [Baka HTTP](https://github.com/bakaphp/http) to handle our Routing 

### Usage

#### Requests

## Ecosystem
When working with other local apps we have created a docker network called `canvas_network` , this will allow other local ecosystem apps to connect to it if needed

Add to your local docker-compose file on the app network

``` 
  my-proxy-net:
    external:
      name: canvas_network
``` 

And on your contianer network info

```
    networks:
      - local-network
      - my-proxy-net
```

### TODO
- Documentation