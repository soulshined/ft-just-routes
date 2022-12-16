<?php

namespace FT\Routing\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class ExceptionHandler {

    public function __construct(public readonly string $value)
    {

    }

}

?>