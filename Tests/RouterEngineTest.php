<?php
namespace Test;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class RouterEngineTest extends TestCase
{
    private $engine;
    private $request;
    function __construct(){
    parent::__construct();

    $dir = __DIR__."/controllers";
    $namespace = "Test\Controllers";

    $collection = new Scrawler\Router\RouteCollection($dir,$namespace);
    $this->request = Request::create(
    '/hello/world/pranjal',
    'GET',
     );

    $this->engine = new Scrawler\Router\RouterEngine($this->request,$collection);
    }

    function testRoute(){
      $this->engine->route();
      $this->assertEquals($this->request->attribute->get('_controller'),'Test\Controllers\Hello::world');
      $this->assertEquals($this->request->attribute->get('_arguments'),'pranjal');
    }

}
