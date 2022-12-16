<?php
namespace FT\Routing;

use Exception;
use FT\Attributes\Reflection\Attribute;
use FT\RequestResponse\Enums\RequestMethods;
use FT\Routing\Attributes\DeleteMapping;
use FT\Routing\Attributes\GetMapping;
use FT\Routing\Attributes\PostMapping;
use FT\Routing\Attributes\PutMapping;
use FT\Routing\Attributes\RequestMapping;
use FT\Routing\Exceptions\RouteException;
use ReflectionMethod;

final class ControllerMethodDescriptor {

    public readonly bool $has_mapping;
    public readonly Route $route;

    public function __construct(
        public readonly ReflectionMethod $delegate
    )
    {
        $request_mapping = $delegate->getAttributes(RequestMapping::class);

        $mappings = [
            ...$delegate->getAttributes(GetMapping::class),
            ...$delegate->getAttributes(PostMapping::class),
            ...$delegate->getAttributes(PutMapping::class),
            ...$delegate->getAttributes(DeleteMapping::class)
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
            $attr = new Attribute($request_mapping[0]);

            $methods = [];
            foreach ($attr->getArgument('methods') as $method)
                $methods[] = $method;

            $this->route = new Route(trim($attr->getArgument('value')), $methods);
        }
        else {
            $mapping = $mappings[0];
            $attr = new Attribute($mapping);

            $method = RequestMethods::GET;
            switch ($mapping->getName()) {
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

            $this->route = new Route(trim($attr->getArgument('value') ?? "/"), [$method]);
        }

    }

    public function invoke(object $controller, array $segments) {
        $placeholders = $this->route->placeholders;
        $args = [];
        foreach ($this->delegate->getParameters() as $param) {
            if (!key_exists($param->name, $placeholders)) continue;

            $args[] = $segments[$placeholders[$param->name]->index];
        }

        $this->delegate->invoke($controller, ...$args);
    }

}
?>