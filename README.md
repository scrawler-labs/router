# Ghost Route
An Fully Automatic, Framework independent, RESTful PHP Router.

Why Ghost Route?
------------------
Ghost Route is an library for automatic restful routing, you do not have to define a single route, it automatically detects the url and calls the corrosponding controler.
Automatic routing is made possible by following some conventions.

Getting Started
----------------
In your index.php
```php
<?php

use Ghost\Route\RouteCollection;
use Ghost\Route\Router;


$dir = /path/to/your/controllers;
$namespace = Namespace\of\your\controllers;

$collection = new RouteCollection($dir,$namespace);
$router = new Router($collection);
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
echo "Hello World";
}

}
```
now calling `localhost/hello/world` from your browser you will see `hello world` on your screen
