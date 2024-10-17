<?php 
arch()->preset()->php();
arch()->preset()->security();
arch()->preset()->strict();

it('tests manual route  ', function (bool $cache): void {


$this->router = new \Scrawler\Router\Router();
$this->router->get('/testo',fn(): string => 'Hello');

$response = $this->router->dispatch('get','/testo');
$response = call_user_func($response[1]);
expect($response)->toBe('Hello');

$this->router->post('/testo',fn(): string => 'Hello post');

$response = $this->router->dispatch('post','/testo');
$response = call_user_func($response[1]);
expect($response)->toBe('Hello post');

$this->router->delete('/testo',fn(): string => 'Hello delete');


$response = $this->router->dispatch('delete','/testo');
$response = call_user_func($response[1]);

expect($response)->toBe('Hello delete');

$this->router->put('/testo',fn(): string => 'Hello put');


$response = $this->router->dispatch('put','/testo');
$response = call_user_func($response[1]);

 expect($response)->toBe('Hello put');

 $this->router->all('/testall',fn(): string => 'Hello all');

$response = $this->router->dispatch('post','/testall');
$response = call_user_func($response[1]);
expect($response)->toBe('Hello all');

$response = $this->router->dispatch('get','/testall');
$response = call_user_func($response[1]);
expect($response)->toBe('Hello all');


})->with(['cacheEnabled'=>true,'cacheDisabled'=>false]);

it('tests manual route with parameters', function (bool $cache): void {

    $this->router = new \Scrawler\Router\Router();

    $this->router->get('/testo/:alpha',fn($name): string => 'Hello '.$name);
    
    [$status,$handler,$args,$debug] = $this->router->dispatch('get','/testo/sam');
    $response = call_user_func($handler,...$args);

    expect($response)->toBe('Hello sam');

    $this->router->get('/test/num/:number',fn($num): string => 'num '.$num);
    
    [$status,$handler,$args,$debug] = $this->router->dispatch('get', '/test/num/5');
    $response = call_user_func($handler,...$args);

    expect($response)->toBe('num 5');

    
    })->with(['cacheEnabled'=>true,'cacheDisabled'=>false]);

    it('tests manual route with parameter advance', function (bool $cache): void {

        $this->router = new \Scrawler\Router\Router();
    
        $this->router->get('/testo/:alpha/test',fn($name): string => 'Hello '.$name);
        
        [$status,$handler,$args,$debug] = $this->router->dispatch('get','/testo/sam/test');
        $response = call_user_func($handler,...$args);
    
        expect($response)->toBe('Hello sam');
    
        $this->router->get('/test/:number/num',fn($num): string => 'num '.$num);
        
        [$status,$handler,$args,$debug] = $this->router->dispatch('get', '/test/5/num');
        $response = call_user_func($handler,...$args);
    
        expect($response)->toBe('num 5');
    
        
        })->with(['cacheEnabled'=>true,'cacheDisabled'=>false]);