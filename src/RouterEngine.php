<?php
declare(strict_types=1);

/**
 * This class routes the URL to corrosponding controller.
 *
 * @author : Pranjal Pandey
 */

namespace Scrawler\Router;


final class RouterEngine
{
    //---------------------------------------------------------------//

    /**
     * Stores the URL broken logic wise.
     *
     * @var array<int,string>
     */
    private array $pathInfo = [];

    /**
     * Stores the request method i.e get,post etc.
     *
     * @var string
     */
    private string $httpMethod;

    /**
     * Stores the request uri.
     *
     * @var string
     */
    private string $uri;

    /**
     * Stores the RouterCollection object.
     * @var RouteCollection
     */
    private RouteCollection $collection;

    /**
     * Stores dir mode
     * @var bool
     */
    private bool $dirMode = false;

    /**
     * Store Dirctory during dir Mode
     * @var string
     */
    private string $dir = '';

    /**
     * stores debug msg
     * @var string
     */
    private string $debugMsg = '';




    //---------------------------------------------------------------//

    /**
     * constructor overloading for auto routing.
     */
    public function __construct(RouteCollection $collection)
    {
        $this->collection = $collection;
    }

    //---------------------------------------------------------------//

    /**
     * Detects the URL and call the corrosponding method
     * of corrosponding controller.
     * @param string $httpMethod
     * @param string $uri
     * @return array<int, mixed>
     */
    public function route(string $httpMethod, string $uri): array
    {

        $this->httpMethod = strtolower($httpMethod);
        $this->uri = $uri;

        //Break URL into segments
        $this->pathInfo = explode('/', $uri);
        array_shift($this->pathInfo);

        //Try manual routing 
        [$status, $handler, $args] = $this->routeManual();
        if ($status) {
            return [1, $handler, $args, ''];
        }

        //Try auto routing
        if ($this->collection->isAutoRegistered()) {
            return $this->routeAuto();
        }

        return [0, '', [], $this->debugMsg];


    }


    /**
     * Set Arguments on the request object.
     * @return array<int, mixed>
     */
    private function routeAuto(): array
    {
        $controller = $this->getController();
        $method = $this->getMethod($controller);
        if ($method == '') {
            if ($this->checkMethodNotAllowed($controller)) {
                return [2, '', [], $this->debugMsg];
            }
            return [0, '', [], $this->debugMsg];
        }
        $handler = $controller . '::' . $method;
        $arguments = $this->getArguments($controller, $method);

        if (is_bool($arguments) && !$arguments) {
            return [0, '', [], $this->debugMsg];
        }

        return [1, $handler, $arguments, ''];
    }


    /**
     * Function to get namespace
     * @return string
     */
    private function getNamespace(): string
    {
        if ($this->dirMode) {
            return $this->collection->getNamespace() . '\\' . $this->dir;
        }

        return $this->collection->getNamespace();
    }

    //---------------------------------------------------------------//

    /**
     * Function to get controller
     * @return string
     */
    private function getController(): string
    {
        $controller = ucfirst($this->pathInfo[0]);

        if (isset($this->pathInfo[0]) && $this->collection->isDir(ucfirst($this->pathInfo[0]))) {
            $this->dir = ucfirst($this->pathInfo[0]);
            $this->dirMode = true;
            array_shift($this->pathInfo);
        }

        if ($this->dirMode && isset($this->pathInfo[0])) {
            $controller = $this->dir . '/' . ucfirst($this->pathInfo[0]);
        }

        //Set corrosponding controller
        if (isset($this->pathInfo[0]) && !empty($this->pathInfo[0])) {
            $controller = $this->collection->getController($controller);
        } else {
            $controller = $this->getNamespace() . '\Main';
        }

        if (!$controller) {
            $controller = '';
        }

        if (class_exists($controller)) {
            return $controller;
        }

        $controller = $this->getNamespace() . '\Main';

        if (class_exists($controller)) {
            array_unshift($this->pathInfo, '');
            return $controller;
        }

        $this->debug('No Controller could be resolved:' . $controller);

        return '';

    }


    /**
     * Function to throw 404 error.
     *
     *@param string $message
     * @return void
     */
    private function debug(string $message): void
    {
        $this->debugMsg = $message;
    }

    //---------------------------------------------------------------//

    /**
     * Function to dispach the method if method exist.
     * @param string $controller
     * @param string $method
     * @return bool|array<mixed>
     */
    private function getArguments(string $controller, string $method): bool|array
    {
        $controllerObj = new $controller;

        $arguments = [];
        for ($j = 2; $j < count($this->pathInfo); $j++) {
            array_push($arguments, $this->pathInfo[$j]);
        }
        //Check weather arguments are passed else throw a 404 error
        $classMethod = new \ReflectionMethod($controllerObj, $method);

        //Optional parameter introduced in version 3.0.2
        if (count($arguments) < count($classMethod->getParameters())) {
            $this->debug('Not enough arguments given to the method');
            return false;
        }
        // finally fix the long awaited allIndex bug !
        if (count($arguments) > count($classMethod->getParameters())) {
            $this->debug('Not able to resolve ' . $method . 'for' . $controller . 'controller');
            return false;
        }

        return $arguments;

    }

    /**
     * Function to check for 405
     * @param string $controller
     * @return bool
     */
    private function checkMethodNotAllowed($controller): bool
    {
        if (!isset($this->pathInfo[1])) {
            return false;
        }
        if (method_exists($controller, 'get' . ucfirst($this->pathInfo[1])) || method_exists($controller, 'post' . ucfirst($this->pathInfo[1])) || method_exists($controller, 'put' . ucfirst($this->pathInfo[1])) || method_exists($controller, 'delete' . ucfirst($this->pathInfo[1]))) {
            return true;
        }
        return false;
    }


    /**
     * Returns the method to be called according to URL.
     *
     * @param string $controller
     *
     * @return string
     */
    private function getMethod(string $controller): string
    {

        //Set Method from second argument from URL
        if (isset($this->pathInfo[1])) {
            if (method_exists($controller, $function = $this->httpMethod . ucfirst($this->pathInfo[1]))) {
                return $function;
            }
            if (method_exists($controller, $function = 'all' . ucfirst($this->pathInfo[1]))) {
                return $function;
            }
        }


        if (isset($function)) {
            $last_function = $function;
        }
        if (method_exists($controller, $function = $this->httpMethod . 'Index')) {
            array_unshift($this->pathInfo, '');
            return $function;
        }
        //Last attempt to invoke allIndex
        if (method_exists($controller, $function = 'allIndex')) {
            array_unshift($this->pathInfo, '');
            return $function;
        }

        if (isset($last_function)) {
            $this->debug('Neither ' . $function . ' method nor ' . $last_function . ' method you found in ' . $controller . ' controller');
            return '';
        }

        $this->debug($function . ' method not found in ' . $controller . ' controller');
        return '';
    }

    /**
     * Function to route manual routes
     * @return array<int, mixed>
     */
    private function routeManual(): array
    {
        $controller = null;
        $arguments = array();
        $routes = $this->collection->getRoutes();
        $collection_route = $this->collection->getRoute($this->uri, $this->httpMethod);
        if ($collection_route) {
            $controller = $collection_route;
        } elseif ($routes) {
            $tokens = array(
                ':string' => '([a-zA-Z]+)',
                ':number' => '([0-9]+)',
                ':alpha' => '([a-zA-Z0-9-_]+)'
            );

            foreach ($routes[$this->httpMethod] as $pattern => $handler_name) {
                $pattern = strtr($pattern, $tokens);
                if (\Safe\preg_match('#^/?' . $pattern . '/?$#', $this->uri, $matches)) {
                    $controller = $handler_name;
                    $arguments = $matches;
                    break;
                }
            }
        }

        if (is_callable($controller)) {
            unset($arguments[0]);
            return [true, $controller, $arguments];
        }

        return [false, '', ''];
    }
}
