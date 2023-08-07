<?php 
use Symfony\Component\HttpFoundation\Request;

it('tests manual route  ', function (bool $cache) {

$collection = getCollection();
$collection->get('/testo',function(){
    return 'Hello';
});

$this->router = new \Scrawler\Router\Router();
$this->router->get('/testo',function(){
    return 'Hello';
});

$response = $this->router->dispatch('get','/testo');
$response = call_user_func($response[1]);
expect($response)->toBe('Hello');

$this->router->post('/testo',function(){
    return 'Hello post';
});

$response = $this->router->dispatch('post','/testo');
$response = call_user_func($response[1]);
expect($response)->toBe('Hello post');

// $collection->delete('/testo',function(){
//     return 'Hello delete';
// });

// $this->router = new \Scrawler\Router\Router($collection, Request::create(
//     '/testo',
//     'DELETE'
// ));

// $response = $this->router->dispatch();

// expect($response->getContent())->toBe('Hello delete');

// $collection->put('/testo',function(){
//     return 'Hello put';
// });

// $this->router = new \Scrawler\Router\Router($collection, Request::create(
//     '/testo',
//     'PUT'
// ));

// $response = $this->router->dispatch();

// expect($response->getContent())->toBe('Hello put');

})->with(['cacheEnabled'=>true,'cacheDisabled'=>false]);

// it('tests manual get with parameters', function (bool $cache) {

//     $collection = getCollection($cache);
//     $collection->get('/testo/:alpha',function($name){
//         return 'Hello '.$name;
//     });

//     $this->router = new \Scrawler\Router\Router($collection, Request::create(
//         '/testo/sam',
//         'GET'
//     ));
    
//     $response = $this->router->dispatch();
    
//     expect($response->getContent())->toBe('Hello sam');

//     $collection->get('/test/num/:number',function($num){
//         return 'num '.$num;
//     });

//     $this->router = new \Scrawler\Router\Router($collection, Request::create(
//         '/test/num/5',
//         'GET'
//     ));
    
//     $response = $this->router->dispatch();
//     expect($response->getContent())->toBe('num 5');

    
//     })->with(['cacheEnabled'=>true,'cacheDisabled'=>false]);