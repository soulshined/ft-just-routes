<?php
namespace FT\Routing;

use FT\Reflection\Method;
use FT\RequestResponse\Enums\RequestMethods;
use FT\RequestResponse\Request;
use FT\Routing\Attributes\DeleteMapping;
use FT\Routing\Attributes\GetMapping;
use FT\Routing\Attributes\PostMapping;
use FT\Routing\Attributes\PutMapping;
use FT\Routing\Attributes\RequestHeader;
use FT\Routing\Attributes\RequestMapping;
use FT\Routing\Attributes\RequestParam;
use FT\Routing\Exceptions\RouteException;

final class ControllerMethodDescriptor {

    public readonly bool $has_mapping;
    public readonly Route $route;

    public function __construct(
        public readonly Method $delegate,
        string $route_prefix
    )
    {
        $request_mapping = $delegate->get_attributes(RequestMapping::class);

        $mappings = [
            ...$delegate->get_attributes(GetMapping::class),
            ...$delegate->get_attributes(PostMapping::class),
            ...$delegate->get_attributes(PutMapping::class),
            ...$delegate->get_attributes(DeleteMapping::class)
        ];

        if (!empty($mappings) && !empty($request_mapping))
            throw new RouteException("Methods can not have a semantic mapping with RequestMapping @ " . $delegate->name);
        else if (count($mappings) > 1)
            throw new RouteException("Methods can not have more than 1 semantic mapping @ " . $delegate->name);
        else if (empty($mappings) && empty($request_mapping)) {
            $this->has_mapping = false;
            return;
        }

        $this->has_mapping = true;

        if (!empty($request_mapping)) {
            $attr = $request_mapping[0];

            $methods = [];
            foreach ($attr->getArgument('methods') as $method)
                $methods[] = $method;

            $this->route = new Route($route_prefix . (Utils::normalize_path($attr->getArgument('value'))), $methods);
        }
        else {
            $mapping = $mappings[0];

            $method = RequestMethods::GET;
            switch ($mapping->name) {
                case PostMapping::class:
                    $method = RequestMethods::POST;
                    break;
                case PutMapping::class:
                    $method = RequestMethods::PUT;
                    break;
                case DeleteMapping::class:
                    $method = RequestMethods::DELETE;
                    break;
            }

            $this->route = new Route($route_prefix . (Utils::normalize_path($mapping->getArgument('value') ?? "/")), [$method]);
        }

    }

    public function invoke(object $controller, array $segments) {
        $req = new Request;
        $placeholders = $this->route->placeholders;
        $args = [];
        foreach ($this->delegate->parameters as $param) {
            if ($param->has_attribute(RequestParam::class)) {
                $reqparam = null;
                $target = $param->get_attribute(RequestParam::class)->getArgument('value');

                if (is_null($target)) $target = $param->name;

                if ($req->isParameterSet($target))
                    $reqparam = $req->parameters->{$target};

                $args[] = $reqparam ?? "";
            }
            else if ($param->has_attribute(RequestHeader::class)) {
                $reqh = null;
                $target = $param->get_attribute(RequestHeader::class)->getArgument('value');

                if (is_null($target)) $target = $param->name;

                if ($req->isHeaderSet($target))
                    $reqh = $req->headers->{$target};

                $args[] = is_scalar($reqh) ? $reqh : $reqh?->raw ?? "";
            }
            else if (!key_exists($param->name, $placeholders)) continue;
            else $args[] = $segments[$placeholders[$param->name]->index]->identifier;
        }

        $this->delegate->invoke($controller, ...$args);
    }

}
?>