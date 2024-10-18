<?php

declare(strict_types=1);

/*
 * This file is part of the Scrawler package.
 *
 * (c) Pranjal Pandey <its.pranjalpandey@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scrawler\Router;

/**
 * This class routes the URL to corrosponding controller.
 */
final class RouterEngine
{
    // ---------------------------------------------------------------//

    /**
     * Stores the URL broken logic wise.
     *
     * @var array<int,string>
     */
    private array $pathInfo = [];

    /**
     * Stores the request method i.e get,post etc.
     */
    private string $httpMethod;

    /**
     * Stores the request uri.
     */
    private string $uri;

    /**
     * Stores dir mode.
     */
    private bool $dirMode = false;

    /**
     * Store Dirctory during dir Mode.
     */
    private string $dir = '';

    /**
     * stores debug msg.
     */
    private string $debugMsg = '';

    // ---------------------------------------------------------------//

    /**
     * constructor overloading for auto routing.
     */
    public function __construct(
        /**
         * Stores the RouterCollection object.
         */
        private readonly RouteCollection $collection,
    ) {
    }

    // ---------------------------------------------------------------//

    /**
     * Detects the URL and call the corrosponding method
     * of corrosponding controller.
     *
     * @return array<int, mixed>
     */
    public function route(string $httpMethod, string $uri): array
    {
        $this->httpMethod = strtolower($httpMethod);
        $this->uri = $uri;

        // Break URL into segments
        $this->pathInfo = explode('/', $uri);
        array_shift($this->pathInfo);

        // Try manual routing
        [$status, $handler, $args] = $this->routeManual();
        if ($status) {
            return [1, $handler, $args, ''];
        }

        // Try auto routing
        if ($this->collection->isAutoRegistered()) {
            return $this->routeAuto();
        }

        return [0, '', [], $this->debugMsg];
    }

    /**
     * Set Arguments on the request object.
     *
     * @return array<int, mixed>
     */
    private function routeAuto(): array
    {
        $controller = $this->getController();
        $method = $this->getMethod($controller);
        if ('' === $method) {
            if ($this->checkMethodNotAllowed($controller)) {
                return [2, '', [], $this->debugMsg];
            }

            return [0, '', [], $this->debugMsg];
        }
        $handler = $controller.'::'.$method;
        $arguments = $this->getArguments($controller, $method);

        if (is_bool($arguments) && !$arguments) {
            return [0, '', [], $this->debugMsg];
        }

        return [1, $handler, $arguments, ''];
    }

    /**
     * Function to get namespace.
     */
    private function getNamespace(): string
    {
        if ($this->dirMode) {
            return $this->collection->getNamespace().'\\'.$this->dir;
        }

        return $this->collection->getNamespace();
    }

    // ---------------------------------------------------------------//

    /**
     * Function to get controller.
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
            $controller = $this->dir.'/'.ucfirst($this->pathInfo[0]);
        }

        // Set corrosponding controller
        if (isset($this->pathInfo[0]) && '' !== $this->pathInfo[0] && '0' !== $this->pathInfo[0]) {
            $controller = $this->collection->getController($controller);
        } else {
            $controller = $this->getNamespace().'\Main';
        }

        if (!$controller) {
            $controller = '';
        }

        if (class_exists($controller)) {
            return $controller;
        }

        $controller = $this->getNamespace().'\Main';

        if (class_exists($controller)) {
            array_unshift($this->pathInfo, '');

            return $controller;
        }

        $this->debug('No Controller could be resolved:'.$controller);

        return '';
    }

    /**
     * Function to throw 404 error.
     */
    private function debug(string $message): void
    {
        $this->debugMsg = $message;
    }

    // ---------------------------------------------------------------//

    /**
     * Function to dispach the method if method exist.
     *
     * @return bool|array<mixed>
     */
    private function getArguments(string $controller, string $method): bool|array
    {
        $controllerObj = new $controller();

        $arguments = [];
        $counter = count($this->pathInfo);
        for ($j = 2; $j < $counter; ++$j) {
            $arguments[] = $this->pathInfo[$j];
        }
        // Check weather arguments are passed else throw a 404 error
        $classMethod = new \ReflectionMethod($controllerObj, $method);
        $params = $classMethod->getParameters();
        // Remove params if it allows null
        foreach ($params as $key => $param) {
            if ($param->isOptional()) {
                unset($params[$key]);
            }
        }
        if (count($arguments) < count($params)) {
            $this->debug('Not enough arguments given to the method');

            return false;
        }
        // finally fix the long awaited allIndex bug !
        if (count($arguments) > count($classMethod->getParameters())) {
            $this->debug('Not able to resolve '.$method.'for'.$controller.'controller');

            return false;
        }

        return $arguments;
    }

    /**
     * Function to check for 405.
     */
    private function checkMethodNotAllowed(string $controller): bool
    {
        if (!isset($this->pathInfo[1])) {
            return false;
        }

        return method_exists($controller, 'get'.ucfirst($this->pathInfo[1])) || method_exists($controller, 'post'.ucfirst($this->pathInfo[1])) || method_exists($controller, 'put'.ucfirst($this->pathInfo[1])) || method_exists($controller, 'delete'.ucfirst($this->pathInfo[1]));
    }

    /**
     * Returns the method to be called according to URL.
     */
    private function getMethod(string $controller): string
    {
        // Set Method from second argument from URL
        if (isset($this->pathInfo[1])) {
            if (method_exists($controller, $function = $this->httpMethod.ucfirst($this->pathInfo[1]))) {
                return $function;
            }
            if (method_exists($controller, $function = 'all'.ucfirst($this->pathInfo[1]))) {
                return $function;
            }
        }

        if (isset($function)) {
            $last_function = $function;
        }
        if (method_exists($controller, $function = $this->httpMethod.'Index')) {
            array_unshift($this->pathInfo, '');

            return $function;
        }
        // Last attempt to invoke allIndex
        if (method_exists($controller, $function = 'allIndex')) {
            array_unshift($this->pathInfo, '');

            return $function;
        }

        if (isset($last_function)) {
            $this->debug('Neither '.$function.' method nor '.$last_function.' method you found in '.$controller.' controller');

            return '';
        }

        $this->debug($function.' method not found in '.$controller.' controller');

        return '';
    }

    /**
     * Function to route manual routes.
     *
     * @return array<int, mixed>
     */
    private function routeManual(): array
    {
        $controller = null;
        $arguments = [];
        $routes = $this->collection->getRoutes();
        $collection_route = $this->collection->getRoute($this->uri, $this->httpMethod);
        if ($collection_route) {
            $controller = $collection_route;
        } elseif ([] !== $routes) {
            $tokens = [
                ':string' => '([a-zA-Z]+)',
                ':number' => '(\d+)',
                ':alpha' => '([a-zA-Z0-9-_]+)',
            ];

            foreach ($routes[$this->httpMethod] as $pattern => $handler_name) {
                $pattern = strtr($pattern, $tokens);
                if (0 !== \Safe\preg_match('#^/?'.$pattern.'/?$#', $this->uri, $matches)) {
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
