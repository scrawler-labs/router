<div align="center">

<h1> Scrawler Router </h1>

<a href="https://github.com/scrawler-labs/router/actions/workflows/main.yml"><img alt="GitHub Workflow Status" src="https://img.shields.io/github/actions/workflow/status/scrawler-labs/router/main.yml?style=flat-square">
</a>
[![Codecov](https://img.shields.io/codecov/c/gh/scrawler-labs/router?style=flat-square)](https://app.codecov.io/gh/scrawler-labs/router)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/quality/g/scrawler-labs/router?style=flat-square)](https://scrutinizer-ci.com/g/scrawler-labs/router/?branch=main)
<a href="[https://github.com/scrawler-labs/router/actions/workflows/main.yml](https://github.com/scrawler-labs/app/actions/workflows/main.yml)"><img src="https://img.shields.io/badge/PHPStan-enabled-brightgreen.svg?style=flat-square" alt="PHPStan Enabled"></a>
[![Packagist Version](https://img.shields.io/packagist/v/scrawler/router?style=flat-square)](https://packagist.org/packages/scrawler/router)
[![Packagist Downloads](https://img.shields.io/packagist/dt/scrawler/router?style=flat-square)](https://packagist.org/packages/scrawler/router)
[![Packagist License](https://img.shields.io/packagist/l/scrawler/router?style=flat-square)](https://packagist.org/packages/scrawler/router)
<br><br>


🔥An Fully Automatic, Framework independent, RESTful PHP Router component🔥<br>
🇮🇳 Made in India 🇮🇳
</div>

![Demo](http://g.recordit.co/lvQba4mnyB.gif)


Complete docs can be found [here](https://component.scrawlerlabs.com/router/)

## 🤔 Why use Scrawler Router?
- Fully automatic, you dont need to define single manual route.
- Support manual route defination for your edge use case.
- No configrations , works out of the box with any php project.
- Stable and well tested.
- Saves lot of time while building RESTful applications
  <br><br>

## 💻 Installation
You can install Scrawler Router via Composer. If you don't have composer installed , you can download composer from [here](https://getcomposer.org/download/)

```sh
composer require scrawler/router
```

## ✨ Setup

Note 4.x release changes the way router handles request and response, if you still wanna continue using old way with symfony components goto [3.x branch](https://github.com/scrawler-labs/router/tree/3.x) 

```php
<?php

use Scrawler\Router\Router;

$dir = '/path/to/your/controllers';
$namespace = 'Namespace\of\your\controllers';

$router = new Router();
// Register your directory for automatic routing
$router->register($dir,$namespace);

/**
* you can now also enable route caching by passing your own PSR 16 implementation
* $cache = new Psr\SimpleCache\CacheInterface();
* $router->enableCache($cache);
**/

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

//Dispatch route and get back the response
[$status,$handler,$args,$debug] = $router->dispatch($httpMethod,$uri);
switch ($status){
  case \Scrawler\Router\Router::NOT_FOUND:
    //handle 404 error
    // $debug contains extra debug info useful to check failure in automatic routing
    break;
  case \Scrawler\Router\Router::METHOD_NOT_ALLOWED:
    //handle 405 method not allowed
    break;
  case \Scrawler\Router\Router::FOUND:
    //call the handler
    $response = call_user_func($handler,...$args);
    // Send Response
    //echo $response
}

```

Done now whatever request occurs it will be automatically routed . You don't have define a single route
<br><br>

## ✏️ Manual routing
Information on manual routing can be found in [docs](https://component.scrawlerlabs.com/router/)
<br><br>


## 🦊 How it Works?

The automatic routing is possible by following some conventions. Lets take a example lets say a controller Hello

```php
<?php
//Hello.php

class Hello
{
    public function getWorld()
    {
        return "Hello World";
    }
}
```
now calling `localhost/hello/world` from your browser you will see `hello world` on your screen.
<br><br>

## 🔥 How does it do it automatically?

Each request to the server is interpreted by Scrawler Router in following way:

`METHOD    /controller/function/arguments1/arguments2`

The controller and function that would be invoked will be

```php
<?php

class Controller
{
    public function methodFunction($arguments1, $arguments2)
    {
        //Definition goes here
    }
}
```
For Example the following call:

`GET  /user/find/1`

would invoke following controller and method

```php
<?php

class User
{
    public function getFind($id)
    {
        //Function definition goes here
    }
}
```
In above example `1` will be passed as argument `$id`
<br><br>

## ⁉️ How should I name my function for automatic routing?

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
Some eg. of <b>valid</b> function names are:
```
getArticles, postUser, putResource
```
<b>Invalid</b> function names are:
```
GETarticles, Postuser, PutResource
```
<br>

## 🏠 Website home page
Scrawler Router uses a special function name `allIndex()` and special controller name `Main`. So If you want to make a controller for your landing page `\` the controller will be defines as follows
```php
// Inside main.php
class Main
{
    // All request to your landing page will be resolved to this controller
    // ALternatively you can use getIndex() to resolve only get request
    public function allIndex()
    {
    }
}
```
<br>

## 🌟 Main Controller
Class name with `Main` signifies special meaning in Scrawler Router , if you wanna define pages route URL you can use main controler
```php
// Inside main.php
class Main
{
    // Resolves `/`
    public function getIndex()
    {
    }
    
    // Resolves `/abc`
    public function getAbc()
    {
    
    }
    
    // Resolves `/hello`
    public function getHello()
    {
    
    }
}
```
<br>

## 👉 Index function
Just like `Main` controller `allIndex(), getIndex(), postIndex()` etc signifies a special meaning , urls with only controller name and no function name will try to resolve into this function.
```php
// Inside hello.php
class Hello
{
    // Resolves `/hello`
    public function getIndex()
    {
    
    }
    
    // Resolves `/hello/abc`
    public function getAbc()
    {
    
    }
}
```
<br>


## 👏 Supporters
If you have reached here consider giving a star to help this project ❤️
[![Stargazers repo roster for @scrawler-labs/router](https://reporoster.com/stars/dark/notext/scrawler-labs/router)](https://github.com/scrawler-labs/router/stargazers)

Thank You for your forks and contributions 
[![Forkers repo roster for @scrawler-labs/router](https://reporoster.com/forks/dark/notext/scrawler-labs/router)](https://github.com/scrawler-labs/router/network/members)
<br><br>

## 🖥️ Server Configuration

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

## 📄 License

Scrawler Router is created by [Pranjal Pandey](https://www.physcocode.com) and released under
the MIT License.
