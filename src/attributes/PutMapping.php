<?php

namespace FT\Routing\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class PutMapping {

    public function __construct(public readonly ?string $value)
    {

    }

}

?>