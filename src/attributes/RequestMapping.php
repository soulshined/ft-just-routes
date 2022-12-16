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

    public function __construct(
        public readonly string $value,
        RequestMethods ...$methods
    ) {
        $this->methods = $methods;
    }

}

?>