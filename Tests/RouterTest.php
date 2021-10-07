<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class RouterTest extends TestCase
{
    private $hello_request;
    private $bye_request;
    private $router;
    function __construct(){
    parent::__construct();

    $dir = __DIR__."/Demo";
    $namespace = "Tests\Demo";

    $collection = new \Scrawler\Router\RouteCollection($dir,$namespace);
    $this->hello_request = Request::create(
    '/hello/world/pranjal',
    'GET'
     );
       
    $this->blank_request = Request::create(
    '/bye/world/nobody',
    'GET'
     );
   

    $this->router = new \Scrawler\Router\Router($collection,$this->request);
    }

    /**
     * @covers Scrawler\Router\Router
     */
    function testDispatch(){
      $response = $this->router->dispatch();
      $this->assertEquals('Hello pranjal',$response->getContent());
      $this->assertEquals('Bye nobody',$response->getContent());

    }


}
