<?php

namespace FT\Routing\Exceptions;

use FT\Attributes\Reflection\ManagedType;

final class RouteAlreadyExistsException extends RouteException {

    public function __construct(string $path, ManagedType $controller)
    {
        parent::__construct("Route $path already exists on " . $controller->name);
    }

}

?>