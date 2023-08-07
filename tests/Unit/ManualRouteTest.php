<?php 
use Symfony\Component\HttpFoundation\Request;

it('tests manual route  ', function (bool $cache) {


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

$this->router->delete('/testo',function(){
    return 'Hello delete';
});


$response = $this->router->dispatch('delete','/testo');
$response = call_user_func($response[1]);

expect($response)->toBe('Hello delete');

$this->router->put('/testo',function(){
    return 'Hello put';
});


$response = $this->router->dispatch('put','/testo');
$response = call_user_func($response[1]);

 expect($response)->toBe('Hello put');

 $this->router->all('/testall',function(){
    return 'Hello all';
});

$response = $this->router->dispatch('post','/testall');
$response = call_user_func($response[1]);
expect($response)->toBe('Hello all');

$response = $this->router->dispatch('get','/testall');
$response = call_user_func($response[1]);
expect($response)->toBe('Hello all');


})->with(['cacheEnabled'=>true,'cacheDisabled'=>false]);

it('tests manual route with parameters', function (bool $cache) {

    $this->router = new \Scrawler\Router\Router();

    $this->router->get('/testo/:alpha',function($name){
        return 'Hello '.$name;
    });
    
    [$status,$handler,$args,$debug] = $this->router->dispatch('get','/testo/sam');
    $response = call_user_func($handler,...$args);

    expect($response)->toBe('Hello sam');

    $this->router->get('/test/num/:number',function($num){
        return 'num '.$num;
    });
    
    [$status,$handler,$args,$debug] = $this->router->dispatch('get', '/test/num/5');
    $response = call_user_func($handler,...$args);

    expect($response)->toBe('num 5');

    
    })->with(['cacheEnabled'=>true,'cacheDisabled'=>false]);