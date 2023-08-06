<?php 
use Symfony\Component\HttpFoundation\Request;

it('tests manual route  ', function (bool $cache) {

$collection = getCollection($cache);
$collection->get('/testo',function(){
    return 'Hello';
});

$router = new \Scrawler\Router\Router($collection, Request::create(
    '/testo',
    'GET'
));

$response = $router->dispatch();

expect($response->getContent())->toBe('Hello');

$collection->post('/testo',function(){
    return 'Hello post';
});

$router = new \Scrawler\Router\Router($collection, Request::create(
    '/testo',
    'POST'
));

$response = $router->dispatch();

expect($response->getContent())->toBe('Hello post');

$collection->delete('/testo',function(){
    return 'Hello delete';
});

$router = new \Scrawler\Router\Router($collection, Request::create(
    '/testo',
    'DELETE'
));

$response = $router->dispatch();

expect($response->getContent())->toBe('Hello delete');

$collection->put('/testo',function(){
    return 'Hello put';
});

$router = new \Scrawler\Router\Router($collection, Request::create(
    '/testo',
    'PUT'
));

$response = $router->dispatch();

expect($response->getContent())->toBe('Hello put');

})->with(['cacheEnabled'=>true,'cacheDisabled'=>false]);

it('tests manual get with parameters', function (bool $cache) {

    $collection = getCollection($cache);
    $collection->get('/testo/:alpha',function($name){
        return 'Hello '.$name;
    });

    $router = new \Scrawler\Router\Router($collection, Request::create(
        '/testo/sam',
        'GET'
    ));
    
    $response = $router->dispatch();
    
    expect($response->getContent())->toBe('Hello sam');

    $collection->get('/testo/:string/hi',function($name){
        return 'Hi '.$name;
    });

    $router = new \Scrawler\Router\Router($collection, Request::create(
        '/testo/pranjal/hi',
        'GET'
    ));
    
    $response = $router->dispatch();
    
    expect($response->getContent())->toBe('Hi pranjal');

    $collection->get('/test/num/:number',function($num){
        return 'num '.$num;
    });

    $router = new \Scrawler\Router\Router($collection, Request::create(
        '/test/num/5',
        'GET'
    ));
    
    $response = $router->dispatch();
    expect($response->getContent())->toBe('num 5');

    
    })->with(['cacheEnabled'=>true,'cacheDisabled'=>false]);

    it('tests manual all ', function (bool $cache) {

        $collection = getCollection($cache);
        $collection->all('/testo/:alpha',function($name){
            return 'Hello '.$name;
        });
    
        $router = new \Scrawler\Router\Router($collection, Request::create(
            '/testo/sam',
            'GET'
        ));
        
        $response = $router->dispatch();
        
        expect($response->getContent())->toBe('Hello sam');
    
        $collection->all('/testo/:string/hi',function($name){
            return 'Hi '.$name;
        });
    
        $router = new \Scrawler\Router\Router($collection, Request::create(
            '/testo/pranjal/hi',
            'GET'
        ));
        
        $response = $router->dispatch();
        
        expect($response->getContent())->toBe('Hi pranjal');
    
        $collection->all('/test/num/:number',function($num){
            return 'num '.$num;
        });
    
        $router = new \Scrawler\Router\Router($collection, Request::create(
            '/test/num/5',
            'POST'
        ));
        
        $response = $router->dispatch();
        expect($response->getContent())->toBe('num 5');
    
        
        })->with(['cacheEnabled'=>true,'cacheDisabled'=>false]);