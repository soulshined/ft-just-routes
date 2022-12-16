<?php

namespace FT\Routing;

use FT\RequestResponse\Enums\RequestMethods;
use FT\Routing\Exceptions\RouteException;

final class Route {

    /**
     * @var RouteSegment[]
     */
    public readonly array $segments;
    public readonly string $path;
    public readonly array $placeholders;

    public function __construct(string $path, public readonly array $http_methods)
    {
        $this->path =  Utils::normalize_path($path);
        if (!str_starts_with($this->path, "/"))
            throw new RouteException("Routes must start with a slash @ $path");

        $this->segments = Utils::get_path_segments($this->path);

        $placeholders = [];
        foreach (array_filter($this->segments, fn ($i) => $i->type === RouteSegmentType::PLACEHOLDER) as $s)
            $placeholders[$s->identifier] = $s;

        $this->placeholders = $placeholders;
    }

    public function equals(Route $route) {
        $has_same_method = !empty(array_intersect(
            array_map(fn ($i) => $i->name, $route->http_methods),
            array_map(fn ($i) => $i->name, $this->http_methods)
        ));

        return $has_same_method && $route->path === $this->path;
    }


    public function matches_request(RequestMethods $method, string $req_path) : bool {
        if (!in_array($method, $this->http_methods))
            return false;

        $req_path = Utils::normalize_path($req_path);
        $req_segments = Utils::get_path_segments($req_path);

        if (count($this->segments) != count($req_segments))
            return false;

        $this_segments = [...$this->segments];
        foreach ($req_segments as $s) {
            $this_s = array_shift($this_segments);

            if ($this_s->type !== RouteSegmentType::PLACEHOLDER &&
                $this_s->identifier !== $s->identifier)
                return false;
        }

        return true;
    }

}

?>