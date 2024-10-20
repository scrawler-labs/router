<?php

arch()->preset()->php();
arch()->preset()->security();
arch()->preset()->strict();


it('tests router dispatch method ', function (bool $cache): void {


    $this->router = new \Scrawler\Router\Router();
    $this->router->register(__DIR__."/../Demo","Tests\Demo");
    [$status,$handler,$args,$debug] = $this->router->dispatch('GET','/hello/world/pranjal');

    $response = call_user_func($handler,...$args);

    expect($status)->toBe(\Scrawler\Router\Router::FOUND);
    expect($response)->toBe('Hello pranjal');

    $collection = getCollection(true);
    $cache = $collection->getCache();
    $this->router->enableCache($cache);
    [$status,$handler,$args,$debug] = $this->router->dispatch('GET','/hello/world');
    expect($status)->toBe(\Scrawler\Router\Router::NOT_FOUND);


    [$status,$handler,$args,$debug] = $this->router->dispatch('GET','/bye/world/nobody');
    $response = call_user_func($handler,...$args);

    expect($status)->toBe(\Scrawler\Router\Router::FOUND);
    expect($response)->toBe('Bye nobody');

})->with(['cacheEnabled'=>true,'cacheDisabled'=>false]);



