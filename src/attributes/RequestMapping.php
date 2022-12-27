<?php

namespace FT\Routing\Attributes;

use Attribute;
use FT\RequestResponse\Enums\RequestMethods;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final class RequestMapping {

    /**
     * @var RequestMethods[]
     */
    public readonly array $methods;

    /**
     * @param null|string $value
     * @param null|string $produces Content-Type of response - the header is automatically set when provided
     * @param RequestMethods[] $methods
     */
    public function __construct(
        public readonly string $value,
        public readonly ?string $produces = null,
        RequestMethods ...$methods
    ) {
        $this->methods = $methods;
    }

}

?>