<?php

namespace FT\Routing\Exceptions;

use RuntimeException;

class RouteException extends RuntimeException {

    public function __construct(string $message)
    {
        parent::__construct($message);
    }

}

?>