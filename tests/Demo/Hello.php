<?php
namespace Tests\Demo;
class Hello{


public function getIndex(string $name): string{
    return "Hello Index".$name;
 }

public function getWorld(string $name): string{
return "Hello ".$name;
}

public function getHi(): string{
    return "Hi";
}

}
