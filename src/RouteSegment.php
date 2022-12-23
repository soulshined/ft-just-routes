<?php

namespace FT\Routing;

final class RouteSegment {

    public readonly string $identifier;
    public readonly RouteSegmentType $type;

    public function __construct(string $segment, public readonly int $index)
    {
        if (str_starts_with($segment, '{') && str_ends_with($segment, '}')) {
            $this->identifier = strtolower(substr($segment, 1, -1));
            $this->type = RouteSegmentType::PLACEHOLDER;
        } else {
            $this->identifier = strtolower($segment);
            $this->type = RouteSegmentType::NORMAL;
        }
    }

}

?>