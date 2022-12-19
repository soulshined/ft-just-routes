<?php

namespace FT\Routing\Exceptions;

use FT\Reflection\Type;

final class RouteAlreadyExistsException extends RouteException {

    public function __construct(string $path, Type $controller)
    {
        parent::__construct("Route $path already exists on " . $controller->name);
    }

}

?>