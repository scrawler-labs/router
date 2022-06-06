<?php

/**
 * Collection of all available controllers.
 *
 * @author : Pranjal Pandey
 */

namespace Scrawler\Router;

class RouteCollection
{

    /**
     * Stores all controller and corrosponding route.
     */
    private $controllers = [];

    /*
     * Stores the path of dirctory containing controllers
     */
    private $directory;

    /*
     * Stores the namespace of controllers
     */
    private $namespace;

    /**
     *  Stores list of directories
     */
    private $dir = [];

    /**
     *  Stores all manual routes
     */
    private $manual = [];

    /**
     *  Stores caching engine
     */
    private $cache;

    /**
     *  Check if caches is enable
     */
    private $enableCache = false;

    //---------------------------------------------------------------//

    public function __construct($directory, $namespace, $enableCache =false, $cache = null)
    {
        $this->directory = $directory;
        $this->namespace = $namespace;
        
        if ($enableCache) {
            if ($cache == null) {
                $this->enableCache();
            } else {
                $this->enableCacheWith($cache);
            }
        }

        if (!$enableCache || !$this->getCache()->has('collection')) {
            $this->autoRegister();
        }
    }

    //---------------------------------------------------------------//

    /**
     * Function returns the class of corrosponding controller.
     *
     * @param string $url
     *
     * @return string
     */
    public function getController($controller)
    {
        if ($this->enableCache && $this->cache->has($controller)) {
            return $this->cache->get($controller);
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
    public function getCache()
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
    public function registerController($name, $class)
    {
        $this->controllers[$name] = $class;
        if ($this->enableCache) {
            $this->cache->set($name, $class);
            $this->cache->set('collection', $this->controllers);
        }
    }

    //---------------------------------------------------------------//

    /**
     * Automatically register all controllers in specified directory.
     */
    private function autoRegister()
    {
        $files = array_slice(scandir($this->directory), 2);
        foreach ($files as $file) {
            if (is_dir($this->directory.'/'.$file)) {
                $this->registerDir($file);
                $dir = $this->directory.'/'.$file;
                $dir_files = array_slice(scandir($dir), 2);
                foreach ($dir_files as $dir_file) {
                    if ($dir_file != 'Main.php' && !\is_dir($dir.'/'.$dir_file)) {
                        $this->registerController($file.'/'.\basename($dir_file, '.php'), $this->namespace . '\\' .\ucfirst($file) . '\\' . \basename($dir_file, '.php'));
                    }
                }
            }
            if ($file != 'Main.php'  && !\is_dir($this->directory.'/'.$file)) {
                $this->registerController(\basename($file, '.php'), $this->namespace . '\\' . \basename($file, '.php'));
            }
        }
        if ($this->enableCache) {
            $this->cache->set('collection', $this->controllers);
        }
    }

    //---------------------------------------------------------------//

    /**
     * Function to get the namespace of controllers
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    //---------------------------------------------------------------//

    /**
     * Function to return list of all controller currently registerd with route collction
     *
     * @return array
     */
    public function getControllers()
    {
        return $this->controllers;
    }

    //---------------------------------------------------------------//
    /**
     * Function to add dir to list of dir
     *
     */
    public function registerDir($dir)
    {
        array_push($this->dir, $dir);
    }

    //---------------------------------------------------------------//
    /**
     * Function to check if cache is enabled
     *
     */
    public function isCacheEnabled()
    {
        return $this->enableCache;
    }
    

    //---------------------------------------------------------------//
    /**
     * Function to check if its a registered dir
     *
     * @return boolean
     */
    public function isDir($dir)
    {
        return in_array($dir, $this->dir);
    }

    //---------------------------------------------------------------//
    /**
     * Register controllers manually
     */
    public function registerRoute($method, $url, $controller)
    {
        $this->manual[$method][$url] =  $controller;
    }
    
    //---------------------------------------------------------------//
    /**
     * Register route  with get method
     */
    public function get($url, $controller)
    {
        $this->manual['get'][$url] =  $controller;
    }

    //---------------------------------------------------------------//
    /**
     * Register route  with post method
     */
    public function post($url, $controller)
    {
        $this->manual['get'][$url] =  $controller;
    }

    //---------------------------------------------------------------//
    /**
     * Register route  with post method
     */
    public function all($url, $controller)
    {
        $this->manual['all'][$url] =  $controller;
    }

    //---------------------------------------------------------------//
    /**
     * Register controllers manually
     */
    public function getRoute($method, $url)
    {
        if (isset($this->manual[$method][$url])) {
            return $this->manual[$method][$url];
        }

        return false;
    }

    //---------------------------------------------------------------//
    /**
     * Enable cache with default cache engine
     */
    public function enableCache()
    {
        $this->cache = new Cache\FileSystemCache();
        $this->enableCache = true;
        if ($this->cache->has('collection')) {
            $this->controllers = $this->cache->get('collection');
        }
        return;
    }

    //---------------------------------------------------------------//
    /**
     * Enable cache with custom cache engine
     */
    public function enableCacheWith($cache)
    {
        if ($cache instanceof Psr\SimpleCache\CacheInterface) {
            $this->cache = $cache;
            $this->enableCache = true;
            if ($this->cache->has('collection')) {
                $this->controllers = $this->cache->get('collection');
            }
            return;
        }
        throw new \Exception('Cache engine must be an instance of Psr\SimpleCache\CacheInterface');
    }
}
