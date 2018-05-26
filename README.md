# Router
[![Build Status](https://travis-ci.org/Scrawler-php/Router.svg?branch=master)](https://travis-ci.org/Scrawler-php/Router)

An Fully Automatic, Framework independent, RESTful PHP Router component used in scrawler.

Why Scrawler Router?
------------------
This is an library for automatic restful routing, you do not have to define a single route, it automatically detects the url and calls the corresponding controller.
Automatic routing is made possible by following some conventions.

Installation
--------------

### Using Composer


```sh
composer require scrawler/router
```


Getting Started
----------------
In your index.php
```php
<?php

use Scrawler\Router\RouteCollection;
use Scrawler\Router\Router;
use Symfony\Component\HttpFoundation\Response;



$dir = /path/to/your/controllers;
$namespace = Namespace\of\your\controllers;

$collection = new RouteCollection($dir,$namespace);
$router = new Router($collection);
//Dispatch route and get back the response
$response = $router->dispatch();
//send response
$response->send();
```

Done now whatever request occurs it will be automatically routed . You dont have define a single route

How it Works?
----------------
The automatic routing is possible by following some conventions. Lets take a example lets say a controller Hello

```php
<?php
//Hello.php

class Hello{

public function getWorld(){
return "Hello World";
}

}
```
now calling `localhost/hello/world` from your browser you will see `hello world` on your screen.

How does it do it automatically?
-----------------
Each request to the server is interpreted by ghost route in following way:

`METHOD    /controller/function/arguments1/arguments2`

The controller and function that would be invoked will be

```php
<?php

class controller{

public function methodFunction(arguments1,arguments2){
//Defination goes here
}

}
```
For Example the following call:

`GET  /user/find/1`

would invoke following controller and method

```php
<?php

class User{

public function getFind($id){
//Function defination goes here
}
}
```
In above example `1` will be passed as argument `$id`

How should I name my function for automatic routing?
----------------------------------------------------

The function name in the controller should be named according to following convention:
`methodFunctionname`
Note:The method should always be written in small and the first word of function name should always start with capital.
Method is the method used while calling url. Valid methods are:

```
all - maps any kind of request method i.e it can be get,post etc
get - mpas url called by GET method
post - maps url called by POST method
put - maps url called by PUT method
delete - maps url called by DELETE method
```
Some eg. of valid function names are:
`getArticles, postUser, putResource`
Invalid function names are:
`GETarticles, Postuser, PutResource`


Server Configuration
----------------------

#### Apache

You may need to add the following snippet in your Apache HTTP server virtual host configuration or **.htaccess** file.

```apacheconf
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond $1 !^(index\.php)
RewriteRule ^(.*)$ /index.php/$1 [L]
```

Alternatively, if you’re lucky enough to be using a version of Apache greater than 2.2.15, then you can instead just use this one, single line:
```apacheconf
FallbackResource /index.php
```

#### IIS

For IIS you will need to install URL Rewrite for IIS and then add the following rule to your `web.config`:
```xml
<?xml version="1.0" encoding="UTF-8"?>
<configuration>
    <system.webServer>
        <rewrite>
          <rule name="Toro" stopProcessing="true">
            <match url="^(.*)$" ignoreCase="false" />
              <conditions logicalGrouping="MatchAll">
                <add input="{REQUEST_FILENAME}" matchType="IsFile" ignoreCase="false" negate="true" />
                <add input="{REQUEST_FILENAME}" matchType="IsDirectory" ignoreCase="false" negate="true" />
                <add input="{R:1}" pattern="^(index\.php)" ignoreCase="false" negate="true" />
              </conditions>
            <action type="Rewrite" url="/index.php/{R:1}" />
          </rule>
        </rewrite>
    </system.webServer>
</configuration>
```

#### Nginx

Under the `server` block of your virtual host configuration, you only need to add three lines.
```conf
location / {
  try_files $uri $uri/ /index.php?$args;
}
```

License
-------
Scrawler Router is created by [Pranjal Pandey](https://www.physcocode.com) and released under
the MIT License.
