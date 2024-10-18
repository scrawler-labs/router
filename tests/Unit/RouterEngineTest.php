<?php 
arch()->preset()->php();
arch()->preset()->security();
arch()->preset()->strict();

it('tests normal controller resolving',function(bool $cache): void{
    
    $collection = getCollection($cache);


    $engine = new \Scrawler\Router\RouterEngine($collection);
    [$status,$handler,$args,$debug] = $engine->route('GET','/hello/world/pranjal');

    expect($handler)->toBe('Tests\Demo\Hello::getWorld');
    expect($args)->toBe(['pranjal']);


    [$status,$handler,$args,$debug] = $engine->route('GET','/bye');

    expect($handler)->toBe('Tests\Demo\Bye::allIndex');


    [$status,$handler,$args,$debug] = $engine->route('GET','/bye/test');

    expect($handler)->toBe('Tests\Demo\Bye::allTest');


  })->with(['cacheDisabled'=>false,'cacheEnabled'=>true]);

it('tests controller resolver inside directory',function(bool $cache): void{
  $collection = getCollection($cache);


  $engine = new \Scrawler\Router\RouterEngine($collection);
  [$status,$handler,$args,$debug] = $engine->route('GET','/app/test');

  expect($handler)->toBe('Tests\Demo\App\Test::getIndex');


  [$status,$handler,$args,$debug] = $engine->route('GET','/app/test/hi');

  expect($handler)->toBe('Tests\Demo\App\Test::getHi');


  [$status,$handler,$args,$debug] = $engine->route('GET','/app');
  expect($handler)->toBe('Tests\Demo\App\Main::getIndex');

})->with(['cacheDisabled'=>false,'cacheEnabled'=>true]);

it('tests main controller resolving',function(bool $cache): void{
  $collection = getCollection($cache);


  $engine = new \Scrawler\Router\RouterEngine($collection);
  [$status,$handler,$args,$debug] = $engine->route('GET','/pranjal');

  expect($handler)->toBe('Tests\Demo\Main::getIndex');
  expect($args)->toBe(['pranjal']);


  [$status,$handler,$args,$debug] = $engine->route('GET','/hi');
  expect($handler)->toBe('Tests\Demo\Main::getHi');

})->with(['cacheDisabled'=>false,'cacheEnabled'=>true]);

it('tests controller not found ',function(): void{

  $engine = new \Scrawler\Router\RouterEngine(getCollection(false));
  [$status,$handler,$args,$debug] = $engine->route('GET','/random/r');
  expect($status)->toBe(0);

  [$status,$handler,$args,$debug] = $engine->route('GET','/appo/random');
  expect($status)->toBe(0);

});

it('tests for method not allowed ',function(): void{

  $engine = new \Scrawler\Router\RouterEngine(getCollection(false));
  [$status,$handler,$args,$debug] = $engine->route('GET','/random/r');
  expect($status)->toBe(0);

  [$status,$handler,$args,$debug] = $engine->route('GET','/appo/random');
  expect($status)->toBe(0);

});

it('tests method not found exception',function(): void{

  $engine = new \Scrawler\Router\RouterEngine(getCollection(false));
  [$status,$handler,$args,$debug] = $engine->route('GET','/test/worl');
  expect($status)->toBe(0);


});

it('tests method not allowed exception',function(): void{

  $engine = new \Scrawler\Router\RouterEngine(getCollection(false));
  [$status,$handler,$args,$debug] = $engine->route('POST','/hello/world/pranjal');

  expect($status)->toBe(\Scrawler\Router\Router::METHOD_NOT_ALLOWED);

});

it('tests no route found exception',function(): void{

  $engine = new \Scrawler\Router\RouterEngine(new \Scrawler\Router\RouteCollection());
  [$status,$handler,$args,$debug] = $engine->route('GET','/random/route');

  expect($status)->toBe(\Scrawler\Router\Router::NOT_FOUND);

});


it('tests method call with optional parameter',function(): void{

  $engine = new \Scrawler\Router\RouterEngine(getCollection(false));
  [$status,$handler,$args,$debug] = $engine->route('GET',uri: '/param');
  expect($handler)->toBe('Tests\Demo\Param::allIndex');
  [$status,$handler,$args,$debug] = $engine->route('GET',uri: '/param/12');
  expect($handler)->toBe('Tests\Demo\Param::allIndex');
  [$status,$handler,$args,$debug] = $engine->route('GET',uri: '/param/test');
  expect($handler)->toBe('Tests\Demo\Param::getTest');
  [$status,$handler,$args,$debug] = $engine->route('GET',uri: '/param/test/12');
  expect($handler)->toBe('Tests\Demo\Param::getTest');

});