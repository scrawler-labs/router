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
 * Collection of all available controllers.
 */
final class RouteCollection
{
    /**
     * Stores all controller and corrosponding route.
     *
     * @var array<mixed>
     */
    private array $controllers = [];

    /**
     * Stores all manual route.
     *
     * @var array<mixed>
     */
    private array $route = [];

    /*
     * Stores the path of dirctory containing controllers
     * @var string
     */
    private string $directory;

    /*
     * Stores the namespace of controllers
     * @var string
     */
    private string $namespace;

    /**
     * Stores list of directories.
     *
     * @var array<mixed>
     */
    private array $dir = [];

    /**
     *  Stores caching engine.
     */
    private \Psr\SimpleCache\CacheInterface $cache;

    /**
     *  Check if caches is enable.
     */
    private bool $enableCache = false;

    /**
     *  Check if auto register is enable.
     */
    private bool $autoRegistered = false;

    public function register(string $directory, string $namespace): void
    {
        $this->autoRegistered = true;
        $this->directory = $directory;
        $this->namespace = $namespace;

        $this->autoRegister();
    }

    /**
     * Function returns the class of corrosponding controller.
     */
    public function getController(string $controller): false|string
    {
        if ($this->enableCache && $this->cache->has(str_replace('/', '_', $controller))) {
            return $this->cache->get(str_replace('/', '_', $controller));
        }

        foreach ($this->controllers as $key => $value) {
            if ($key == $controller) {
                return $value;
            }
        }

        return false;
    }

    /**
     * Returns cache engine.
     */
    public function getCache(): \Psr\SimpleCache\CacheInterface
    {
        return $this->cache;
    }

    /**
     * Register controller with route collection.
     */
    public function registerController(string $name, string $class): void
    {
        $this->controllers[$name] = $class;
        if ($this->enableCache) {
            $this->cache->set(str_replace('/', '_', $name), $class);
            $this->cache->set('collection', $this->controllers);
        }
    }

    /**
     * Automatically register all controllers in specified directory.
     */
    private function autoRegister(): void
    {
        $files = array_slice(\Safe\scandir($this->directory), 2);
        foreach ($files as $file) {
            if (is_dir($this->directory.'/'.$file)) {
                $this->registerDir($file);
                $dir = $this->directory.'/'.$file;
                $dir_files = array_slice(\Safe\scandir($dir), 2);
                foreach ($dir_files as $dir_file) {
                    if ('Main.php' != $dir_file && !\is_dir($dir.'/'.$dir_file)) {
                        $this->registerController($file.'/'.\basename((string) $dir_file, '.php'), $this->namespace.'\\'.\ucfirst((string) $file).'\\'.\basename((string) $dir_file, '.php'));
                    }
                }
            }
            if ('Main.php' != $file && !\is_dir($this->directory.'/'.$file)) {
                $this->registerController(\basename((string) $file, '.php'), $this->namespace.'\\'.\basename((string) $file, '.php'));
            }
        }
    }

    /**
     * Function to get the namespace of controllers.
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * Function to return list of all controller currently registerd with route collction.
     *
     * @return array<mixed>
     */
    public function getControllers(): array
    {
        return $this->controllers;
    }

    /**
     * Function to add dir to list of dir.
     */
    public function registerDir(string $dir): void
    {
        $this->dir[] = $dir;

        if ($this->enableCache) {
            $this->cache->set('dir', $this->dir);
        }
    }

    /**
     * Function to check if cache is enabled.
     */
    public function isCacheEnabled(): bool
    {
        return $this->enableCache;
    }

    /**
     * Function to check if its a registered dir.
     */
    public function isDir(string $dir): bool
    {
        if ($this->enableCache && $this->cache->has('dir')) {
            $this->dir = $this->cache->get('dir');
        }

        return in_array($dir, $this->dir);
    }

    /**
     * Enable cache with custom cache engine.
     */
    public function enableCache(\Psr\SimpleCache\CacheInterface $cache): void
    {
        $this->cache = $cache;
        $this->enableCache = true;
        if ($this->cache->has('collection')) {
            $this->controllers = $this->cache->get('collection');
        }
    }

    /**
     * Register manual route in route collection.
     */
    private function registerManual(string $method, string $route, callable $callable): void
    {
        $this->route[$method][$route] = $callable;
    }

    /**
     * register manual get route.
     */
    public function get(string $route, callable $callable): void
    {
        $this->registerManual('get', $route, $callable);
    }

    /**
     * register manual post route.
     */
    public function post(string $route, callable $callable): void
    {
        $this->registerManual('post', $route, $callable);
    }

    /**
     * register manual put route.
     */
    public function put(string $route, callable $callable): void
    {
        $this->registerManual('put', $route, $callable);
    }

    /**
     * register manual delete route.
     */
    public function delete(string $route, callable $callable): void
    {
        $this->registerManual('delete', $route, $callable);
    }

    /**
     * register manual patch route.
     */
    public function all(string $route, callable $callable): void
    {
        $this->registerManual('all', $route, $callable);
    }

    /**
     * get callable using route and method.
     */
    public function getRoute(string $route, string $method): callable|bool
    {
        return $this->route[$method][$route] ?? $this->route['all'][$route] ?? false;
    }

    /**
     * get all routes.
     *
     * @return array<mixed>
     */
    public function getRoutes(): array
    {
        return $this->route;
    }

    /**
     * check if auto register is enable.
     */
    public function isAutoRegistered(): bool
    {
        return $this->autoRegistered;
    }
}
