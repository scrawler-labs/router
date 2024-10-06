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
     * @var RouteCollection
     */
    private RouteCollection $collection;

    /**
     * Stores the Engine Instance.
     * @var RouterEngine
     */
    private RouterEngine $engine;


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
     * @param string $dir
     * @param string $namespace
     * @return void
     */
    public function register(string $dir, string $namespace): void
    {
        $this->collection->register($dir, $namespace);
    }

    //---------------------------------------------------------------//

    /**
     * Enable cache
     * @param \Psr\SimpleCache\CacheInterface $cache
     * @return void
     */
    public function enableCache(\Psr\SimpleCache\CacheInterface $cache): void
    {
        $this->collection->enableCache($cache);
    }

    //---------------------------------------------------------------//
    /**
     * Dispatch function
     * @param string $httpMethod
     * @param string $uri
     * @return array<int, mixed>
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

    /**
     * register manual get route
     * @param string $route
     * @param callable $callable
     * @return void
     */
    public function get(string $route, callable $callable): void
    {
        $this->collection->get($route, $callable);
    }

    /**
     * Register manual post route
     * @param string $route
     * @param callable $callable
     * @return void
     */
    public function post(string $route, callable $callable): void
    {
        $this->collection->post($route, $callable);
    }

    /**
     * Register manual put route
     * @param string $route
     * @param callable $callable
     * @return void
     */
    public function put(string $route, callable $callable): void
    {
        $this->collection->put($route, $callable);
    }

    /**
     * Register manual delete route
     * @param string $route
     * @param callable $callable
     * @return void
     */
    public function delete(string $route, callable $callable): void
    {
        $this->collection->delete($route, $callable);
    }

    /**
     * Register manual all route
     * @param string $route
     * @param callable $callable
     * @return void
     */
    public function all(string $route, callable $callable): void
    {
        $this->collection->all($route, $callable);
    }
}
