<?php

namespace FT\Routing\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class PostMapping {

    public function __construct(public readonly ?string $value)
    {

    }

}

?>