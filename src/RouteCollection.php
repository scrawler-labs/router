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
     */
    private string $directory;

    /*
     * Stores the namespace of controllers
     */
    private string $namespace;

    /**
     *  Stores list of directories
     */
    private array $dir = [];

    /**
     *  Stores caching engine
     */
    private \Psr\SimpleCache\CacheInterface $cache;

    /**
     *  Check if caches is enable
     */
    private bool $enableCache = false;

    /**
     *  Check if auto register is enable
     */
    private bool $autoRegistered = false;


    //---------------------------------------------------------------//

    public function register(string $directory, string $namespace): void
    {
        $this->autoRegistered = true;
        $this->directory = $directory;
        $this->namespace = $namespace;

        $this->autoRegister();
    }

    //---------------------------------------------------------------//

    /**
     * Function returns the class of corrosponding controller.
     *
     * @param string $controller
     *
     * @return string|bool
     */
    public function getController(string $controller): bool|string
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
    //---------------------------------------------------------------//
    /**
     * Returns cache engine
     *
     */
    public function getCache(): \Psr\SimpleCache\CacheInterface
    {
        return $this->cache;
    }
    //---------------------------------------------------------------//

    /**
     * Register the list of controllers.
     *
     * @param string $name
     * @param string $class
     */
    public function registerController(string $name, string $class): void
    {
        $this->controllers[$name] = $class;
        if ($this->enableCache) {
            $this->cache->set(str_replace('/', '_', $name), $class);
            $this->cache->set('collection', $this->controllers);
        }
    }

    //---------------------------------------------------------------//

    /**
     * Automatically register all controllers in specified directory.
     */
    private function autoRegister(): void
    {
        $files = array_slice(scandir($this->directory), 2);
        foreach ($files as $file) {
            if (is_dir($this->directory . '/' . $file)) {
                $this->registerDir($file);
                $dir = $this->directory . '/' . $file;
                $dir_files = array_slice(scandir($dir), 2);
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

    //---------------------------------------------------------------//

    /**
     * Function to get the namespace of controllers
     *
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    //---------------------------------------------------------------//

    /**
     * Function to return list of all controller currently registerd with route collction
     *
     * @return array<mixed>
     */
    public function getControllers(): array
    {
        return $this->controllers;
    }

    //---------------------------------------------------------------//
    /**
     * Function to add dir to list of dir
     *
     */
    public function registerDir(string $dir): void
    {
        array_push($this->dir, $dir);

        if ($this->enableCache) {
            $this->cache->set('dir', $this->dir);
        }

    }

    //---------------------------------------------------------------//
    /**
     * Function to check if cache is enabled
     *
     */
    public function isCacheEnabled(): bool
    {
        return $this->enableCache;
    }


    //---------------------------------------------------------------//
    /**
     * Function to check if its a registered dir
     *
     * @return boolean
     */
    public function isDir(string $dir): bool
    {
        if ($this->enableCache && $this->cache->has('dir')) {
            $this->dir = $this->cache->get('dir');
        }
        return in_array($dir, $this->dir);
    }


    //---------------------------------------------------------------//
    /**
     * Enable cache with custom cache engine
     */
    public function enableCache(\Psr\SimpleCache\CacheInterface $cache): void
    {
        $this->cache = $cache;
        $this->enableCache = true;
        if ($this->cache->has('collection')) {
            $this->controllers = $this->cache->get('collection');
        }
    }

    //---------------------------------------------------------------//
    private function registerManual(string $method, string $route, callable $callable): void
    {
        $this->route[$method][$route] = $callable;
    }

    //---------------------------------------------------------------//
    public function get(string $route, callable $callable): void
    {
        $this->registerManual('get', $route, $callable);
    }

    //---------------------------------------------------------------//
    public function post(string $route, callable $callable): void
    {
        $this->registerManual('post', $route, $callable);
    }

    //---------------------------------------------------------------//
    public function put(string $route, callable $callable): void
    {
        $this->registerManual('put', $route, $callable);
    }

    //---------------------------------------------------------------//
    public function delete(string $route, callable $callable): void
    {
        $this->registerManual('delete', $route, $callable);
    }
    //---------------------------------------------------------------//
    public function all(string $route, callable $callable): void
    {
        $this->registerManual('all', $route, $callable);
    }

    //---------------------------------------------------------------//
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

    //---------------------------------------------------------------//
    public function getRoutes(): array
    {
        return $this->route;
    }

    //---------------------------------------------------------------//
    public function isAutoRegistered(): bool
    {
        return $this->autoRegistered;
    }
}



