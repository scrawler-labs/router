<?php
use Symfony\Component\HttpFoundation\Request;


it('tests router dispatch method ', function (bool $cache) {

    $collection = getCollection($cache);

    $router = new \Scrawler\Router\Router($collection, Request::create(
        '/hello/world/pranjal',
        'GET'
    ));
    $response = $router->dispatch();

    expect($response->getContent())->toBe('Hello pranjal');
    $router = new \Scrawler\Router\Router($collection, Request::create(
        '/bye/world/nobody',
        'GET'
    ));
    $response = $router->dispatch();

    expect($response->getContent())->toBe('Bye nobody');

})->with(['cacheEnabled'=>true,'cacheDisabled'=>false]);



