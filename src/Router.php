<?php
/**
 * This class is used when it is used as stand alone router
 *
 * @author : Pranjal Pandey
 */

namespace Scrawler\Router;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Router
{
    //---------------------------------------------------------------//

    /**
     * Stores the Request Object
     */
    private $request;

    /**
     * Stores the RouterCollection object.
     */
    private $collection;

    /**
     * Stores the Engine Instance.
     */
    private $engine;

    //---------------------------------------------------------------//

    /**
     * constructor overloading for auto routing.
     */
    public function __construct(RouteCollection $collection = null, Request $request = null)
    {
        if ($request == null) {
            $this->request = Request::createFromGlobals();
        } else {
            $this->request = $request;
        }
        
        if(is_null($collection)){
            $this->collection = new RouteCollection();
        }else{
            $this->collection = $collection;
        }
        $this->engine = new RouterEngine($this->request, $this->collection);
        $this->collection = $this->engine->getCollection();
    }

    //---------------------------------------------------------------//
    /**
     * Dispatch function
     */
    public function dispatch($type = null)
    {
        $this->engine->route();
        $controller = new ControllerResolver();
        $arguments = new ArgumentResolver();

        $controller = $controller->getController($this->request);
        $arguments = $arguments->getArguments($this->request, $controller);
        $content = call_user_func($controller,...$arguments);
        if ($type == null) {
            $type = array('content-type' => 'text/html');
        }
        if (!$content instanceof \Symfony\Component\HttpFoundation\Response) {
            $response = new Response($content, Response::HTTP_OK, $type);
        } else {
            $response = $content;
        }

        $response->prepare($this->request);
        return $response;
    }

    public function get($route,$callable)
    {
        $this->collection->get($route,$callable);
    }

    public function post($route,$callable){
        $this->collection->post($route,$callable);
    }

    public function put($route,$callable){
        $this->collection->put($route,$callable);
    }

    public function delete($route,$callable){
        $this->collection->delete($route,$callable);
    }

    public function all($route,$callable){
        $this->collection->all($route,$callable);
    }


}
