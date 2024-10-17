<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    // uncomment to reach your current PHP version
    ->withPhpSets(php83:true)
    ->withAttributesSets()
    ->withPreparedSets(deadCode:true,typeDeclarations:true,codeQuality:true);
