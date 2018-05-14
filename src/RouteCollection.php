<?php

/**
 * Collection of all available controllers.
 *
 * @author : Pranjal Pandey
 */

namespace Scrawler\Router;

class RouteCollection {

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

    //---------------------------------------------------------------//

    public function __construct($directory, $namespace) {
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
    public function getController($controller) {
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
    public function registerController($name, $class) {
        $this->controllers[$name] = $class;
    }

    //---------------------------------------------------------------//

    /**
     * Automatically register all controllers in specified directory.
     */
    private function autoregister() {
        $files = array_slice(scandir($this->directory), 2);
        foreach ($files as $file) {
            if ($file != 'Main.php') {
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
    public function getNamespace(){
        return $this->namespace;
    }

    //---------------------------------------------------------------//

    /**
     * Function to return list of all controller currently registerd with route collction
     *
     * @return array
     */
    public function getControllers(){
        return $this->controllers;
    }
}
