<?php

/**
 * Argument Resolver Class
 *
 * @author : Pranjal Pandey
 */

namespace Scrawler\Router;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;

class ArgumentResolver implements ArgumentResolverInterface {

  public function getArguments(Request $request, $controller=null){
    $arguments=$request->attributes->get('__arguments');
    return explode(",",$arguments);
  }

}
