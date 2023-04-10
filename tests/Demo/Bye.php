<?php
namespace Tests\Demo;
use Symfony\Component\HttpFoundation\Response;

class Bye{

public function allIndex(){
    return "Bye";
}
    
public function allTest(){
    return "Test";
}
    
public function getWorld($name){
return new Response("Bye ".$name, Response::HTTP_OK, array('content-type' => 'text/html'));
}

}
