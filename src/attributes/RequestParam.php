<?php

namespace FT\Routing\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
final class RequestParam {

    public function __construct( public readonly ?string $value ) { }

}
