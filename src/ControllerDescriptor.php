<?php
namespace FT\Routing;

use FT\Reflection\Attribute;
use FT\Reflection\Type;
use FT\RequestResponse\Enums\RequestMethods;
use FT\Routing\Attributes\ExceptionHandler;
use FT\Routing\Exceptions\RouteAlreadyExistsException;

final class ControllerDescriptor {

    /**
     * @var ControllerMethodDescriptor[]
     */
    private array $methods = [];
    public array $exception_handlers = [];

    public function __construct(
        public readonly Type $type
    )
    {
        foreach ($type->delegate->getMethods() as $method) {
            $md = new ControllerMethodDescriptor($method);
            if (!$md->has_mapping) {
                $ehs = $md->delegate->getAttributes(ExceptionHandler::class);
                foreach ($ehs as $eh) {
                    $attr = new Attribute($eh);
                    $this->exception_handlers[$attr->getArgument('value')] = $md;
                }

                continue;
            }

            foreach ($this->methods as $this_md) {
                if ($md->route->equals($this_md->route))
                    throw new RouteAlreadyExistsException($md->route->path, $type);
            }

            $this->methods[] = $md;
        }
    }

    public function get_route(RequestMethods $method, string $path) : ?ControllerMethodDescriptor {
        foreach ($this->methods as $md)
            if ($md->route->matches_request($method, $path)) return $md;

        return null;
    }

}
?>