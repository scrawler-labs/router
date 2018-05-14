<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class RouterTest extends TestCase
{
    private $request;
    private $router;
    function __construct(){
    parent::__construct();

    $dir = __DIR__."/Demo";
    $namespace = "Tests\Demo";

    $collection = new \Scrawler\Router\RouteCollection($dir,$namespace);
    $this->request = Request::create(
    '/hello/world/pranjal',
    'GET'
     );

    $this->router = new \Scrawler\Router\Router($collection,$this->request);
    }

    /**
     *
     * @covers Scrawler\Router\Router
     */
    function testDispatch(){
      $result=$this->router->dispatch();
      $this->assertEquals('Hello pranjal',$result);
    }


}
