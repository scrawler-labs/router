<?php

/**
 * This class is used when it is used as stand alone router
 *
 * @author : Pranjal Pandey
 */

namespace Scrawler\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Router{
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
    public function __construct(RouteCollection $collection,Request $request = null) {
        if($request == null)
        $this->request = Request::createFromGlobals();
        else
        $this->request = $request;
        $this->collection = $collection;
        $this->engine = new RouterEngine($this->request,$this->collection);
        $this->engine->route();
    }

    //---------------------------------------------------------------//
      /**
       * Dispatch function
       */
      public function dispatch(){
         $controller = new ControllerResolver();
         $arguments = new ArgumentResolver();

         $controller = $controller->getController($this->request);
         $arguments = $arguments->getArguments($this->request);
         $response = new Response(
         'Content',
          Response::HTTP_OK,
          array('content-type' => 'text/html')
          );
         $response->setContent(call_user_func_array($controller,$arguments));
         $response->prepare($this->request);
         return $response;
      }



}
