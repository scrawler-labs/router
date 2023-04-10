<?php
use Symfony\Component\HttpFoundation\Request;


it('tests router dispatch method ', function (bool $cache) {

    $collection = getCollection($cache);

    $this->router = new \Scrawler\Router\Router($collection, Request::create(
        '/hello/world/pranjal',
        'GET'
    ));
    $response = $this->router->dispatch();

    expect($response->getContent())->toBe('Hello pranjal');
    $this->router = new \Scrawler\Router\Router($collection, Request::create(
        '/bye/world/nobody',
        'GET'
    ));
    $response = $this->router->dispatch();

    expect($response->getContent())->toBe('Bye nobody');

})->with(['cacheEnabled'=>true,'cacheDisabled'=>false]);

