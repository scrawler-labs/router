<?php
declare(strict_types=1);

/**
 * This class is used when it is used as stand alone router
 *
 * @author : Pranjal Pandey
 */

namespace Scrawler\Router;


final class Router
{
    //---------------------------------------------------------------//

    /**
     * Stores the RouterCollection object.
     */
    private $collection;

    /**
     * Stores the Engine Instance.
     */
    private $engine;


    public const NOT_FOUND = 0;
    public const FOUND = 1;
    public const METHOD_NOT_ALLOWED = 2;

    //---------------------------------------------------------------//

    /**
     * constructor overloading for auto routing.
     */
    public function __construct()
    {

        $this->collection = new RouteCollection();
        $this->engine = new RouterEngine($this->collection);
    }

    //---------------------------------------------------------------//

    /**
     * constructor overloading for auto routing.
     */
    public function register(string $dir, string $namespace): void
    {
        $this->collection->register($dir, $namespace);
    }

    //---------------------------------------------------------------//

    /**
     * constructor overloading for auto routing.
     */
    public function enableCache(\Psr\SimpleCache\CacheInterface $cache): void
    {
        $this->collection->enableCache($cache);
    }

    //---------------------------------------------------------------//
    /**
     * Dispatch function
     */
    public function dispatch(string $httpMethod, string $uri): array
    {
        $result = $this->engine->route($httpMethod, $uri);

        if ($result[0] == 0 || $result[0] == 2) {
            return $result;
        }

        if (\is_callable($result[1])) {
            return $result;
        }

        [$class, $method] = explode('::', $result[1], 2);
        $result[1] = [new $class(), $method];

        return $result;
    }

    //---------------------------------------------------------------//
    public function get(string $route, callable $callable): void
    {
        $this->collection->get($route, $callable);
    }

    //---------------------------------------------------------------//
    public function post(string $route, callable $callable): void
    {
        $this->collection->post($route, $callable);
    }

    //---------------------------------------------------------------//
    public function put(string $route, callable $callable): void
    {
        $this->collection->put($route, $callable);
    }

    //---------------------------------------------------------------//
    public function delete(string $route, callable $callable): void
    {
        $this->collection->delete($route, $callable);
    }
    //---------------------------------------------------------------//
    public function all(string $route, callable $callable): void
    {
        $this->collection->all($route, $callable);
    }
}
