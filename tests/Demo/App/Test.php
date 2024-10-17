<?php
namespace Tests\Demo\App;
use Symfony\Component\HttpFoundation\Response;

class Test{

public function getIndex(): string{
return 'This is dir test';
}

public function getHi(): string{
    return 'This is dir hi';
}

}
