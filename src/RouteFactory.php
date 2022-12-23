<?php

namespace FT\Routing;

use FT\Reflection\ClassCache;
use FT\RequestResponse\Enums\RequestMethods;
use FT\RequestResponse\Enums\StatusCodes;
use FT\RequestResponse\Request;
use FT\Routing\Attributes\RequestMapping;
use FT\Routing\Exceptions\RouteAlreadyExistsException;
use FT\Routing\Exceptions\RouteException;
use Throwable;

final class RouteFactory {

    /**
     * @var ControllerDescriptor[]
     */
    private static array $controllers = [];
    private static ?object $not_found_handler = null;
    private static array $throwable_handlers = [];
    private static array $middleware_handlers = [];

    public static function registerController(string ...$controllers) {

        foreach ($controllers as $controller) {
            $cls = ClassCache::get($controller);

            $path_attr = $cls->get_attribute(RequestMapping::class);
            if ($path_attr === null)
                throw new RouteException("Controllers require #[RequestMapping] annotations @ " . $cls->name);

            $segments = Utils::get_path_segments(Utils::normalize_path($path_attr->getArgument('value')));

            if (array_first(fn ($i) => $i->type === RouteSegmentType::PLACEHOLDER, $segments) !== null)
                throw new RouteException("Controller #[RequestMapping] can not contain path variable placeholders @ " . $cls->name);

            $controller = new ControllerDescriptor($cls, "/" . join("/", array_map(fn ($i) => $i->identifier, $segments)));

            foreach (static::$controllers as $c) {
                foreach ($controller->methods as $m1) {
                    foreach ($c->methods as $m2) {
                        if ($m2->route->equals($m1->route))
                            throw new RouteAlreadyExistsException($controller->type->shortname . "::" . $m1->route->path, $c->type);
                    }
                }
            }

            static::$controllers[] = $controller;
        }

    }

    public static function onNotFound(callable $callable) {
        static::$not_found_handler = $callable;
    }

    public static function onException(Throwable | string $throwable, callable $callable) {
        $throwable = is_object($throwable) ? $throwable::class : $throwable;

        static::$throwable_handlers[$throwable] = $callable;
    }

    public static function beforeEach(callable $callable) {
        static::$middleware_handlers[] = $callable;
    }

    public static function dispatch() {
        $req = new Request;

        $path = Utils::normalize_path($req->url->path);

        foreach (static::$controllers as $controller) {
            $md = $controller->get_route(RequestMethods::tryFromName($_SERVER['REQUEST_METHOD']), $path);

            if ($md === null) continue;

            foreach (static::$middleware_handlers as $handler)
                call_user_func($handler, $req->url->path);

            $cd_instance = $controller->type->delegate->newInstance();

            try {
                $md->invoke($cd_instance, Utils::get_path_segments($path));
                return;
            } catch (\Throwable $th) {
                if (!empty(static::$throwable_handlers) || !empty($controller->exception_handlers)) {
                    foreach ($controller->exception_handlers as $eh => $eh_md) {
                        if ($eh === $th::class) {
                            $eh_md->delegate->invoke($cd_instance, $th, $req->url->path);
                            return;
                        }
                    }

                    foreach (static::$throwable_handlers as $key => $callable) {
                        if ($key === $th::class) {
                            call_user_func($callable, $req->url->path);
                            return;
                        }
                    }
                }

                throw $th;
            }
        }

        static::handle_not_found($req->url->path);
    }

    private static function handle_not_found(string $path) {
        if (isset(static::$not_found_handler)) {
            call_user_func(static::$not_found_handler, $path);
            return;
        }

        http_response_code(StatusCodes::NOT_FOUND->value);
        die;
    }
}

?>