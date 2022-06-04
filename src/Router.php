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
    public function __construct(RouteCollection $collection, Request $request = null)
    {
        if ($request == null) {
            $this->request = Request::createFromGlobals();
        } else {
            $this->request = $request;
        }
        
        $this->collection = $collection;
        $this->engine = new RouterEngine($this->request, $this->collection);
        $this->engine->route();
    }

    //---------------------------------------------------------------//
    /**
     * Dispatch function
     */
    public function dispatch($type = null)
    {
        $controller = new ControllerResolver();
        $arguments = new ArgumentResolver();

        $controller = $controller->getController($this->request);
        $arguments = $arguments->getArguments($this->request, $controller);
        $content = $controller(...$arguments);
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
}
