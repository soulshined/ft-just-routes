<?php

namespace FT\Routing;

use Exception;
use FT\Attributes\ClassCache;
use FT\RequestResponse\Enums\RequestMethods;
use FT\RequestResponse\Enums\StatusCodes;
use FT\RequestResponse\Request;
use FT\Routing\Attributes\RequestMapping;
use FT\Routing\Exceptions\RouteAlreadyExistsException;
use FT\Routing\Exceptions\RouteException;
use Throwable;

final class RouteFactory {

    private static array $cache = [];
    private static ?object $not_found_handler = null;
    private static array $throwable_handlers = [];
    private static array $middleware_handlers = [];

    public static function registerController(string ...$controllers) {

        foreach ($controllers as $controller) {
            $cls = ClassCache::get($controller);

            $path_attr = $cls->get_attribute(RequestMapping::class);
            if ($path_attr === null)
                throw new RouteException("Controllers require #[RequestMapping] annotations @ " . $cls->name);

            $path = Utils::normalize_path($path_attr->getArgument('value'));
            if (key_exists($path, static::$cache))
                throw new RouteAlreadyExistsException($cls->shortname . "::$path", static::$cache[$path]->delegate);

            static::$cache[$path] = new ControllerDescriptor($cls);
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

        $segments = preg_split("/\//", $req->url->path, -1, PREG_SPLIT_NO_EMPTY);

        $path = Utils::normalize_path("/" . array_shift($segments));

        if (!key_exists($path, static::$cache)) {
            static::handle_not_found($req->url->path);
            return;
        }

        /**
         * @var ControllerDescriptor
         */
        $cd = static::$cache[$path];

        $md = $cd->get_route(RequestMethods::tryFromName($_SERVER['REQUEST_METHOD']), "/" . join("/", $segments));

        if ($md === null)
            static::handle_not_found($req->url->path);

        else {
            foreach (static::$middleware_handlers as $handler)
                call_user_func($handler, $req->url->path);

            $cd_instance = $cd->delegate->delegate->newInstance();

            try {
                $md->invoke($cd_instance, $segments);
            } catch (\Throwable $th) {
                if (!empty(static::$throwable_handlers) || !empty($cd->exception_handlers)) {
                    foreach ($cd->exception_handlers as $eh => $eh_md) {
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