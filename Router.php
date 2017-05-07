<?php

/**
 * This class routes the URL to corrosponding controller.
 *
 * @author : Pranjal Pandey
 */

namespace Ghost\Route;

class Router {
    //---------------------------------------------------------------//

    /**
     * Stores the URL broken logic wise.
     *
     * @var array
     */
    private $path_info = [];

    /**
     * Stores the request method i.e get,post etc.
     *
     * @var string
     */
    private $request_method;

    /**
     * Stores the RouterCollection object.
     */
    private $collection;

//---------------------------------------------------------------//

    /**
     * constructor overloading for auto routing.
     */
    public function __construct(RouterCollection $collection) {
        $this->$collection = $collection;
        $this->route();
    }

//---------------------------------------------------------------//
    

    /**
     * Detects the URL and call the corrosponding method
     * of corrosponding controller.
     */
    protected function route() {
        // Get URL and request method.
        $this->request_method = strtolower($_SERVER['REQUEST_METHOD']);
        $this->path_info = (strpos($_SERVER['REQUEST_URI'], '?') > 0) ? strstr($_SERVER['REQUEST_URI'], '?', true) : $_SERVER['REQUEST_URI'];

        //Break URL into segments
        if ($this->path_info === 'Index.php') {
            $this->path_info = '/';
        }
        $this->path_info = explode('/', $this->path_info);
        array_shift($this->path_info);

        /*Todo: Fixing problems with subdir
        if (defined('SUBDIR') && SUBDIR == $this->path_info[0]) {
            array_splice($this->path_info, 0, 1);
        }*/

        //Set corrosponding controller
        if (isset($this->path_info[0]) && !empty($this->path_info[0])) {
            $controller = $collection->getController(ucfirst($this->path_info[0]));
        } else {
            $controller = $collection->getNamespace().'\Main';
        }

        //Dispach the method according to URL
        if (class_exists($controller)) {
            $controller = new $controller();
            $function = $this->getMethod($controller);
            $this->dispatch($controller, $function);
        } else {
            $controller = $collection->getNamespace().'\Main';
            array_unshift($this->path_info, '');
            $function = $this->getMethod($controller);
            $this->dispatch($controller, $function);
        }
    }

//---------------------------------------------------------------//

    /**
     * Function to throw 404 error.
     */
    protected function error() {
        throw new NotFondException('oops its an 404 error');
    }

//---------------------------------------------------------------//

    /**
     * Function to dispach the method if method exist.
     *
     * @param string $controller
     * @param string $method
     */
    protected function dispatch($controller, $method) {
        if (method_exists($controller, $method)) {
            //Set Arguments from URL
            $i = count($this->path_info);
            $arguments = [];
            for ($j = 2; $j < $i; $j++) {
                array_push($arguments, $this->path_info[$j]);
            }

            //Check weather arguments are passed else throw a 404 error
            $classMethod = new \ReflectionMethod($controller, $method);
            $argumentCount = count($classMethod->getParameters());
            if (count($arguments) < $argumentCount) {
                $this->error();
            } else {
                //Finally call the function
                $result = call_user_func_array([$controller, $method], $arguments);
            }
        }
    }

//---------------------------------------------------------------//

    /**
     * Returns the method to be called according to URL.
     *
     * @param string $controller
     *
     * @return string
     */
    protected function getMethod($controller) {

        //Set Method from second argument from URL
        if (isset($this->path_info[1])) {
            $function = $this->request_method . ucfirst($this->path_info[1]);
            if (method_exists($controller, $function)) {
                return $function;
            } elseif (method_exists($controller, 'all' . ucfirst($this->path_info[1]))) {
                return 'all' . ucfirst($this->path_info[1]);
            } else {
                $this->error();
            }
        }
        //If second argument not set switch to Index function
        else {
            $function = $this->request_method . 'Index';
            if (method_exists($controller, $function)) {
                return $function;
            } elseif (method_exists($controller, 'allIndex')) {
                return 'allIndex';
            } else {
                $this->error();
            }
        }
    }

//---------------------------------------------------------------//
}
