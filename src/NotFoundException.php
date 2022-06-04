<?php

/**
 * An Exception to be thrown on 404 error
 *
 * @author : Pranjal Pandey
 */
namespace Scrawler\Router;

use Throwable;

class NotFoundException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        http_response_code(404);
        parent::__construct($message, $code, $previous);
    }
}
