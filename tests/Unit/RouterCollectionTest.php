<?php

it('tests getController() method',function(bool $cache){
      $collection = getCollection($cache);
      expect($collection->getController('Hello'))->toBe('Tests\Demo\Hello');
      expect($collection->getController('Bye'))->toBe('Tests\Demo\Bye');
})->with(['cacheEnabled'=>true,'cacheDisabled'=>false]);

it('tests registerController() method',function(bool $cache){
    $collection = getCollection($cache);
    $collection->registerController('Test','Tests\Demo\Test');
    expect($collection->getController('Test'))->toBe('Tests\Demo\Test');
  })->with(['cacheEnabled'=>true,'cacheDisabled'=>false]);

it('tests getNamepace() method',function(bool $cache){
    $collection = getCollection($cache);
    expect($collection->getNamespace())->toBe('Tests\Demo');
  })->with(['cacheEnabled'=>true,'cacheDisabled'=>false]);

it('tests getControllers() method',function(bool $cache){
    $collection = getCollection($cache);
    expect($collection->getControllers())->toHaveKey('Hello');
  })->with(['cacheEnabled'=>true,'cacheDisabled'=>false]);
