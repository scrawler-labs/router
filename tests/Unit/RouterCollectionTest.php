<?php

it('tests getController() method',function(bool $cache): void{
      $collection = getCollection($cache);
      expect($collection->getController('Hello'))->toBe(\Tests\Demo\Hello::class);
      expect($collection->getController('Bye'))->toBe(\Tests\Demo\Bye::class);
})->with(['cacheEnabled'=>true,'cacheDisabled'=>false]);

it('tests registerController() method',function(bool $cache): void{
    $collection = getCollection($cache);
    $collection->registerController('Test',\Tests\Demo\Test::class);
    expect($collection->getController('Test'))->toBe(\Tests\Demo\Test::class);
  })->with(['cacheEnabled'=>true,'cacheDisabled'=>false]);

it('tests getNamepace() method',function(bool $cache): void{
    $collection = getCollection($cache);
    expect($collection->getNamespace())->toBe('Tests\Demo');
  })->with(['cacheEnabled'=>true,'cacheDisabled'=>false]);

it('tests getControllers() method',function(bool $cache): void{
    $collection = getCollection($cache);
    expect($collection->getControllers())->toHaveKey('Hello');
  })->with(['cacheEnabled'=>true,'cacheDisabled'=>false]);

  it('tests cache related methods',function(): void{
    $collection = getCollection(true);
    expect($collection->getCache())->toBeInstanceOf(\Psr\SimpleCache\CacheInterface ::class);
    expect($collection->isCacheEnabled())->toBe(true);
  });

  it('tests for dir',function(): void{
    $collection = getCollection(true);
    //expect($collection->getCache())->toBeInstanceOf(Kodus\Cache\FileCache::class);
    expect($collection->isDir('App'))->toBe(true);
  });