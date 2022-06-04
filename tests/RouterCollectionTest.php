<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class RouterCollectionTest extends TestCase
{
    private $collection;
    function __construct(){
    parent::__construct();

    $dir = __DIR__."/Demo";
    $namespace = "Tests\Demo";

    $this->collection = new \Scrawler\Router\RouteCollection($dir,$namespace);
    }

    function testGetController(){
      $this->assertEquals('Tests\Demo\Hello',$this->collection->getController('Hello'));
      $this->assertEquals('Tests\Demo\Bye',$this->collection->getController('Bye'));
    }

    function testRegisterController(){
      $this->collection->registerController('Test','Tests\Demo\Test');
      $this->assertEquals('Tests\Demo\Test',$this->collection->getController('Test'));
    }

    function testGetNamespace(){
      $this->assertEquals('Tests\Demo',$this->collection->getNamespace());
    }

    function testGetControllers(){
      $this->assertArrayHasKey('Hello', $this->collection->getControllers());
    }


}
