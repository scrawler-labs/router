<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers \Scrawler\Router\RouterEngine
 */
class RouterEngineTest extends TestCase
{
    private $engine;
    private $request;
    function __construct(){
    parent::__construct();

    $dir = __DIR__."/Demo";
    $namespace = "Tests\Demo";

    $collection = new \Scrawler\Router\RouteCollection($dir,$namespace);
    $this->request = Request::create(
    '/hello/world/pranjal',
    'GET'
     );

    $this->engine = new \Scrawler\Router\RouterEngine($this->request,$collection);
    }


    function testRoute(){
      $this->engine->route();
      $this->assertEquals('Tests\Demo\Hello::getWorld',$this->request->attributes->get('_controller'));
      $this->assertEquals('pranjal',$this->request->attributes->get('_arguments'));
    }



}
