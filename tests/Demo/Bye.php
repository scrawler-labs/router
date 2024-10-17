<?php
namespace Tests\Demo;

class Bye{

public function allIndex(): string{
    return "Bye";
}
    
public function allTest(): string{
    return "Test";
}
    
public function getWorld(string $name): string{
    return "Bye ".$name;
}

}
