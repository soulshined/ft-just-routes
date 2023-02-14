<?php

namespace FT\Routing\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class PutMapping {

    /**
     * @param null|string $value
     * @param null|string $produces Content-Type of response - the header is automatically set when provided
     */
    public function __construct(public readonly ?string $value, public readonly ?string $produces = null)
    {

    }

}

?>