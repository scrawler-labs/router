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
 * This class is used when it is used as stand alone router.
 */
final readonly class Router
{
    // ---------------------------------------------------------------//

    /**
     * Stores the RouterCollection object.
     */
    private RouteCollection $collection;

    /**
     * Stores the Engine Instance.
     */
    private RouterEngine $engine;

    public const NOT_FOUND = 0;
    public const FOUND = 1;
    public const METHOD_NOT_ALLOWED = 2;

    // ---------------------------------------------------------------//

    /**
     * constructor overloading for auto routing.
     */
    public function __construct()
    {
        $this->collection = new RouteCollection();
        $this->engine = new RouterEngine($this->collection);
    }

    // ---------------------------------------------------------------//

    /**
     * constructor overloading for auto routing.
     */
    public function register(string $dir, string $namespace): void
    {
        $this->collection->register($dir, $namespace);
    }

    // ---------------------------------------------------------------//

    /**
     * Enable cache.
     */
    public function enableCache(\Psr\SimpleCache\CacheInterface $cache): void
    {
        $this->collection->enableCache($cache);
    }

    // ---------------------------------------------------------------//
    /**
     * Dispatch function.
     *
     * @return array<int, mixed>
     */
    public function dispatch(string $httpMethod, string $uri): array
    {
        $result = $this->engine->route($httpMethod, $uri);

        if (0 == $result[0] || 2 == $result[0]) {
            return $result;
        }

        if (\is_callable($result[1])) {
            return $result;
        }

        [$class, $method] = explode('::', (string) $result[1], 2);
        $result[1] = [new $class(), $method];

        return $result;
    }

    /**
     * register manual get route.
     */
    public function get(string $route, callable $callable): void
    {
        $this->collection->get($route, $callable);
    }

    /**
     * Register manual post route.
     */
    public function post(string $route, callable $callable): void
    {
        $this->collection->post($route, $callable);
    }

    /**
     * Register manual put route.
     */
    public function put(string $route, callable $callable): void
    {
        $this->collection->put($route, $callable);
    }

    /**
     * Register manual delete route.
     */
    public function delete(string $route, callable $callable): void
    {
        $this->collection->delete($route, $callable);
    }

    /**
     * Register manual all route.
     */
    public function all(string $route, callable $callable): void
    {
        $this->collection->all($route, $callable);
    }
}
