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

    //---------------------------------------------------------------//

    public function __construct($directory, $namespace)
    {
        $this->directory = $directory;
        $this->namespace = $namespace;
        $this->autoregister();
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
        foreach ($this->controllers as $key => $value) {
            if ($key == $controller) {
                return $value;
            }
        }

        return false;
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
    }

    //---------------------------------------------------------------//

    /**
     * Automatically register all controllers in specified directory.
     */
    private function autoregister()
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
}
