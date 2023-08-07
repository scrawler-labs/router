<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

// uses(Tests\TestCase::class)->in('Feature');

function getCollection($cache){
    $collection = new \Scrawler\Router\RouteCollection();
    if($cache){
        $cache = new Kodus\Cache\FileCache(__DIR__.'/cache',10); 
        $collection->enableCache($cache);
    }
    $collection->register(__DIR__."/Demo","Tests\Demo");

    return $collection;
}
