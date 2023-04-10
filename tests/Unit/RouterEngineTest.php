<?php 
use Symfony\Component\HttpFoundation\Request;

it('tests normal controller resolving',function(bool $cache){
    
    $collection = getCollection($cache);

    $request = Request::create(
        '/hello/world/pranjal',
        'GET'
         );
    $engine = new \Scrawler\Router\RouterEngine($request,$collection);
    $engine->route();

    expect($request->attributes->get('_controller'))->toBe('Tests\Demo\Hello::getWorld');
    expect($request->attributes->get('_arguments'))->toBe('pranjal');

    $request = Request::create(
      '/bye',
      'GET'
       );

    $engine = new \Scrawler\Router\RouterEngine($request,$collection);
    $engine->route();

    expect($request->attributes->get('_controller'))->toBe('Tests\Demo\Bye::allIndex');

    $request = Request::create(
      '/bye/test',
      'GET'
       );

    $engine = new \Scrawler\Router\RouterEngine($request,$collection);
    $engine->route();

    expect($request->attributes->get('_controller'))->toBe('Tests\Demo\Bye::allTest');


  })->with(['cacheDisabled'=>false,'cacheEnabled'=>true]);

it('tests controller resolver inside directory',function(bool $cache){
  $collection = getCollection($cache);

  $request = Request::create(
    '/app/test',
    'GET'
  );
  $engine = new \Scrawler\Router\RouterEngine($request,$collection);
  $engine->route();

  expect($request->attributes->get('_controller'))->toBe('Tests\Demo\App\Test::getIndex');

  $request = Request::create(
    '/app/test/hi',
    'GET'
  );
  $engine = new \Scrawler\Router\RouterEngine($request,$collection);
  $engine->route();

  expect($request->attributes->get('_controller'))->toBe('Tests\Demo\App\Test::getHi');

  $request = Request::create(
    '/app',
    'GET'
  );
  $engine = new \Scrawler\Router\RouterEngine($request,$collection);
  $engine->route();
  expect($request->attributes->get('_controller'))->toBe('Tests\Demo\App\Main::getIndex');

})->with(['cacheDisabled'=>false,'cacheEnabled'=>true]);

it('tests main controller resolving',function(bool $cache){
  $collection = getCollection($cache);

  if($collection->isCacheEnabled()){
    $collection->getCache()->clear();
  }

  $request = Request::create(
      '/pranjal',
      'GET'
       );
  $engine = new \Scrawler\Router\RouterEngine($request,$collection);
  $engine->route();

  expect($request->attributes->get('_controller'))->toBe('Tests\Demo\Main::getIndex');
  expect($request->attributes->get('_arguments'))->toBe('pranjal');

  $request = Request::create(
    '/hi',
    'GET'
     );
  $engine = new \Scrawler\Router\RouterEngine($request,$collection);
  $engine->route();
  expect($request->attributes->get('_controller'))->toBe('Tests\Demo\Main::getHi');

})->with(['cacheDisabled'=>false,'cacheEnabled'=>true]);

it('tests controller not found exception',function(){
  $request = Request::create(
    '/random/r',
    'GET'
     );
  $engine = new \Scrawler\Router\RouterEngine($request,getCollection());
  $engine->route();
})->throws(\Scrawler\Router\NotFoundException::class);

it('tests method not found exception',function(){

  $request = Request::create(
    '/test/worl',
    'GET'
     );
  $engine = new \Scrawler\Router\RouterEngine($request,getCollection());
  $engine->route();

})->throws(\Scrawler\Router\NotFoundException::class);

it('tests argument not found exception',function(){

  $request = Request::create(
    '/bye/world',
    'GET'
     );
  $engine = new \Scrawler\Router\RouterEngine($request,getCollection());
  $engine->route();
  
})->throws(\Scrawler\Router\NotFoundException::class);