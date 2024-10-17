<?php
namespace Tests\Demo;
class Main{

public function getIndex(string $name): string{
return "Main Index Test ".$name;
}

public function getHi(): string{
    return "Main Hi Test";
}

}
