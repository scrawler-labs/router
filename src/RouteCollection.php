<?php
declare(strict_types=1);

/**
 * Collection of all available controllers.
 *
 * @author : Pranjal Pandey
 */

namespace Scrawler\Router;

final class RouteCollection
{

    /**
     * Stores all controller and corrosponding route.
     * @var array<mixed>
     */
    private array $controllers = [];

    /**
     * Stores all manual route.
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
     * Stores list of directories
     * @var array<mixed>
     */
    private array $dir = [];

    /**
     *  Stores caching engine
     */
    private \Psr\SimpleCache\CacheInterface $cache;

    /**
     *  Check if caches is enable
     * @var bool
     */
    private bool $enableCache = false;

    /**
     *  Check if auto register is enable
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
     *
     * @param string $controller
     *
     * @return string|false
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
     * Returns cache engine
     * @return \Psr\SimpleCache\CacheInterface
     */
    public function getCache(): \Psr\SimpleCache\CacheInterface
    {
        return $this->cache;
    }

    /**
     * Register controller with route collection.
     *
     * @param string $name
     * @param string $class
     * @return void
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
     * @return void
     */
    private function autoRegister(): void
    {
        $files = array_slice(\Safe\scandir($this->directory), 2);
        foreach ($files as $file) {
            if (is_dir($this->directory . '/' . $file)) {
                $this->registerDir($file);
                $dir = $this->directory . '/' . $file;
                $dir_files = array_slice(\Safe\scandir($dir), 2);
                foreach ($dir_files as $dir_file) {
                    if ($dir_file != 'Main.php' && !\is_dir($dir . '/' . $dir_file)) {
                        $this->registerController($file . '/' . \basename($dir_file, '.php'), $this->namespace . '\\' . \ucfirst($file) . '\\' . \basename($dir_file, '.php'));
                    }
                }
            }
            if ($file != 'Main.php' && !\is_dir($this->directory . '/' . $file)) {
                $this->registerController(\basename($file, '.php'), $this->namespace . '\\' . \basename($file, '.php'));
            }
        }

    }



    /**
     * Function to get the namespace of controllers
     *
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }


    /**
     * Function to return list of all controller currently registerd with route collction
     *
     * @return array<mixed>
     */
    public function getControllers(): array
    {
        return $this->controllers;
    }

    /**
     * Function to add dir to list of dir
     * @param string $dir
     * @return void
     */
    public function registerDir(string $dir): void
    {
        array_push($this->dir, $dir);

        if ($this->enableCache) {
            $this->cache->set('dir', $this->dir);
        }

    }

    /**
     * Function to check if cache is enabled
     * @return bool
     */
    public function isCacheEnabled(): bool
    {
        return $this->enableCache;
    }


    /**
     * Function to check if its a registered dir
     * @param string $dir
     * @return bool
     */
    public function isDir(string $dir): bool
    {
        if ($this->enableCache && $this->cache->has('dir')) {
            $this->dir = $this->cache->get('dir');
        }
        return in_array($dir, $this->dir);
    }


    /**
     * Enable cache with custom cache engine
     * @param \Psr\SimpleCache\CacheInterface $cache
     * @return void
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
     * Register manual route in route collection
     * @param string $method
     * @param string $route
     * @param callable $callable
     * @return void
     */
    private function registerManual(string $method, string $route, callable $callable): void
    {
        $this->route[$method][$route] = $callable;
    }

    /**
     * register manual get route
     * @param string $route
     * @param callable $callable
     * @return void
     */
    public function get(string $route, callable $callable): void
    {
        $this->registerManual('get', $route, $callable);
    }

    /**
     * register manual post route
     * @param string $route
     * @param callable $callable
     * @return void
     */
    public function post(string $route, callable $callable): void
    {
        $this->registerManual('post', $route, $callable);
    }

    /**
     * register manual put route
     * @param string $route
     * @param callable $callable
     * @return void
     */
    public function put(string $route, callable $callable): void
    {
        $this->registerManual('put', $route, $callable);
    }

    /**
     * register manual delete route
     * @param string $route
     * @param callable $callable
     * @return void
     */
    public function delete(string $route, callable $callable): void
    {
        $this->registerManual('delete', $route, $callable);
    }

    /**
     * register manual patch route
     * @param string $route
     * @param callable $callable
     * @return void
     */
    public function all(string $route, callable $callable): void
    {
        $this->registerManual('all', $route, $callable);
    }

    /**
     * get callable using route and method
     * @param string $route
     * @param string $method
     * @return callable|bool
     */
    public function getRoute(string $route, string $method): callable|bool
    {
        if (isset($this->route[$method][$route])) {
            return $this->route[$method][$route];
        }
        if (isset($this->route['all'][$route])) {
            return $this->route['all'][$route];
        }
        return false;
    }

    /**
     * get all routes
     * @return array<mixed>
     */
    public function getRoutes(): array
    {
        return $this->route;
    }

    /**
     * check if auto register is enable
     * @return bool
     */
    public function isAutoRegistered(): bool
    {
        return $this->autoRegistered;
    }
}



