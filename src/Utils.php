<?php

namespace FT\Routing;

use FT\Routing\Exceptions\RouteException;

final class Utils {

    public static function normalize_path(string $path) : string {
        $path = trim($path);

        if (!str_starts_with($path, "/"))
            throw new RouteException("Routes must start with a slash @ $path");

        if (str_ends_with($path, '/')) $path = substr($path, 0, -1);
        if (empty($path)) $path = "/";

        return $path;
    }

    public static function get_path_segments(string $path) : array {
        $segments = preg_split("/\//", $path, -1, PREG_SPLIT_NO_EMPTY);

        $result = [];
        $placeholders = [];
        for ($i=0; $i < count($segments); $i++) {
            $s = new RouteSegment($segments[$i], $i);

            if ($s->type === RouteSegmentType::PLACEHOLDER) {
                if (in_array($s->identifier, $placeholders))
                    throw new RouteException("Routes can not have duplicate placeholders @ $path");

                $placeholders[] = $s->identifier;
            }

            $result[] = $s;
        }

        return $result;
    }

}

?>