<?php

/**
 * This class routes the URL to corrosponding controller.
 *
 * @author : Pranjal Pandey
 */

namespace Scrawler\Router;
use Symfony\Component\HttpFoundation\Request;


class RouterEngine {
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
     * Stores the Request Object
     */
     private $request;

    /**
     * Stores the RouterCollection object.
     */
    private $collection;

    /**
     * Stores the controller being dispatched
     */
    private $controller;

    /**
     * Store the method bring dispatched
     */
    private $method;

//---------------------------------------------------------------//

    /**
     * constructor overloading for auto routing.
     */
    public function __construct(Request $request, RouteCollection $collection) {
        $this->request = $request;
        $this->collection = $collection;
    }

//---------------------------------------------------------------//


    /**
     * Detects the URL and call the corrosponding method
     * of corrosponding controller.
     */
    public function route() {
        // Get URL and request method.
        $this->request_method = strtolower($this->request->getMethod());
        $this->path_info = $this->request->getPathInfo();

        //Break URL into segments
        if ($this->path_info === 'Index.php') {
            $this->path_info = '/';
        }

        $this->path_info = explode('/', $this->path_info);
        array_shift($this->path_info);



        //Set corrosponding controller
        if (isset($this->path_info[0]) && !empty($this->path_info[0])) {
            $this->controller = $this->collection->getController(ucfirst($this->path_info[0]));
        } else {
            $this->controller = $this->collection->getNamespace().'\Main';
        }

        //Sets the Request attribute according to the route
        if (class_exists($this->controller)) {
            $this->method = $this->getMethod($this->controller);
            $this->request->attributes->set('_controller',$this->controller.'::'.$this->method);
        } else {
            $this->controller = $this->collection->getNamespace().'\Main';
            array_unshift($this->path_info, '');
            $this->method = $this->getMethod($this->controller);
            $this->request->attributes->set('_controller',$this->controller.'::'.$this->method);

            }
            $this->setArguments();
    }

//---------------------------------------------------------------//

    /**
     * Function to throw 404 error.
     *
     *@param string $message
     */
    protected function error($message) {
        $controllers = $this->collection->getControllers();
        $list="";
        foreach($controllers as $name => $class){
        $list = $list.''.$name.':'.$class;
        }
        throw new NotFoundException('Oops its an 404 error! :'.$message.' List of currently registerd controllers:'.$list);
    }

//---------------------------------------------------------------//

    /**
     * Function to dispach the method if method exist.
     *
     */
    private function setArguments() {
        $controller = new $this->controller;
        $method = $this->method;

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
                $this->error('Not enough arguments given to the method');
            } else {
                //set arguments
                $this->request->attributes->set('_arguments',implode(",",$arguments));

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
                $this->error('The'.$function.'method you are looking for is not found in given controller');
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
                $this->error('The index method could not be found in given controller');
            }
        }
    }

//---------------------------------------------------------------//

  /**
   * Returns fully qualified class name of controller being dipatched
   *
   * @return String
   */
    public function getRoutedController(){
        return $this->controller;
    }

//----------------------------------------------------------------//
    /*
     * Returns the method to be called
     *
     * @return String
     */
    public function getRoutedMethod(){
        return $this->method;
    }

}
