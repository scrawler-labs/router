<?php

/**
 * Argument Resolver Class
 *
 * @author : Pranjal Pandey
 */

namespace Scrawler\Router;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpFoundation\Request;

class ArgumentResolver implements ArgumentResolverInterface {

  public function getArguments(Request $request, $controller=null){
    $arguments=$request->attributes->get('_arguments');
    return explode(",",$arguments);
  }

}
