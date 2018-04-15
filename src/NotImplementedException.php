<?php

/**
 * An Exception to be thrown on 404 error
 * 
 * @author : Pranjal Pandey
 */
namespace Ghost\Route;

use Throwable;

class NotImplementedException extends \Exception {

    public function __construct(string $message = "", int $code = 0, Throwable $previous = null) {
        http_response_code(501);
        parent::__construct($message, $code, $previous);
    }
}
