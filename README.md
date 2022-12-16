# Just Routes

A extremely lightweight, fast and focused PHP library strictly for routing requests. No hassle, no framework required.

Just attributes, plain old PHP, and just routes

## Usage

1. `composer require ft/just-routes`
2. [Create controller[s]](#create-controllers)
3. [Register controller[s]](#create-controllers)
4. [Add optional methods](#customize)
5. [Dispatch Request](#the-end)


### Create Controllers

```php
final class MyController {

}
```

1. Annotate your controller with request mapping

```php
#[RequestMapping(value: "/foobar")]
final class MyController {

}
```

2. Add routes as methods to controller
```php
#[RequestMapping(value: "/foobar")]
final class MyController {

    #[GetMapping]
    function voidMethod() { // maps to GET /foobar
    }

    #[GetMapping("/bazz")]
    function get_bazz() { // maps to GET /foobar/bazz
        echo "bazz";
    }

}
```

### Register Controllers

```php
RouteFactory::registerController(MyController::class);
```

### Customize

You can customize a few control flow patterns:

1. Exceptions
2. Not Found Paths

#### Exceptions

You can catch exceptions at the controller layer or globally via RouteFactory

scoped exception handling via controller annotation
```php
#[RequestMapping(value: "/foobar")]
final class MyController {

    #[GetMapping]
    function voidMethod() { // maps to GET /foobar
        throw new IllegalArgumentException("Illegal");
    }

    #[GetMapping("/bazz")]
    function get_bazz() { // maps to GET /foobar/bazz
        throw new IllegalArgumentException("Illegal");
    }

    #[ExceptionHandler(IllegalArgumentException::class)]
    function handle_illegal_arg_exc(IllegalArgumentException $exc, string $path) {
        //swallowed IllegalArgumentException only from this controller's routes
    }

}
```

globally catching exceptions

```php
RouteFactory::registerController(MyController::class);
RouteFactory::onException(IllegalArgumentException::class, function (string $path) {
    echo "Caught globally";
});
```

#### Not Found

globally handle

```php
RouteFactory::registerController(MyController::class);
RouteFactory::onNotFound(function ($path) {
    echo "$path not found";
});
```

### The End

That's it to get routing configured. Now simply dispatch the request

```php
RouteFactory::registerController(MyController::class);
RouteFactory::dispatch();
```

### Miscellaneous

#### Semantic Attributes

- #[GetMapping]
- #[PutMapping]
- #[PostMapping]
- #[DeleteMapping]

#### Other Attributes

- #[RequestMapping]
- #[ExceptionHandler]

#### Route Syntax

Routes are separated by `/`, must start with `/` and must not duplicate for HTTP method types

Routes may contain path variables. A path variable is encapsulated by `{}` curly braces.

Routes with path variables must have parameters in the route method signature

For example:

```php
#[RequestMapping(value: "/foobar")]
final class MyController {

    #[GetMapping("/name/{name}/age/{age}")]
    function get_for_name_age(string $name, int $age) {
        // example req
        // GET /foobar/name/John/age/18
    }

}
```