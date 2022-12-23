<?php

use FT\Attributes\Validation\IllegalArgumentException;
use FT\RequestResponse\Enums\RequestMethods;
use FT\Routing\Attributes\DeleteMapping;
use FT\Routing\Attributes\ExceptionHandler;
use FT\Routing\Attributes\GetMapping;
use FT\Routing\Attributes\PostMapping;
use FT\Routing\Attributes\PutMapping;
use FT\Routing\Attributes\RequestMapping;

include __DIR__ . '/../vendor/autoload.php';

#[RequestMapping(value: "/good/foo/bazz/buzz")]
final class GoodControllerWithPrefix {

    #[GetMapping]
    function get_no_path() {
        echo "Prefixed Hello World";
    }

    #[RequestMapping(value: "/reqmap", methods: [RequestMethods::GET, RequestMethods::HEAD, RequestMethods::TRACE])]
    function request_mapping() {
        echo "Prefixed Hello from " . $_SERVER['REQUEST_METHOD'];
    }

    #[GetMapping("/marco")]
    function get_with_path() {
        echo "Prefixed Polo";
    }

    #[GetMapping("/code/{code}/number/{number}")]
    function get_with_path_vars(string $code, int $number) {
        echo "Prefixed Code: $code | Number: $number";
    }

    #[GetMapping("/throw_illegal_arg_exception")]
    function get_that_throws() {
        throw new IllegalArgumentException("Illegal");
    }

    #[PostMapping]
    function post_no_path()
    {
        echo "Prefixed Hello World";
    }

    #[PostMapping("/marco")]
    function post_with_path()
    {
        echo "Prefixed Polo";
    }

    #[PostMapping("/code/{code}/number/{number}")]
    function post_with_path_vars(string $code, int $number)
    {
        echo "Prefixed Code: $code | Number: $number";
    }

    #[PostMapping("/throw_illegal_arg_exception")]
    function post_that_throws()
    {
        throw new IllegalArgumentException("Illegal");
    }

    #[PutMapping]
    function put_no_path()
    {
        echo "Prefixed Hello World";
    }

    #[PutMapping("/marco")]
    function put_with_path()
    {
        echo "Prefixed Polo";
    }

    #[PutMapping("/code/{code}/number/{number}")]
    function put_with_path_vars(string $code, int $number)
    {
        echo "Prefixed Code: $code | Number: $number";
    }

    #[PutMapping("/throw_illegal_arg_exception")]
    function put_that_throws()
    {
        throw new IllegalArgumentException("Illegal");
    }

    #[DeleteMapping]
    function delete_no_path()
    {
        echo "Prefixed Hello World";
    }

    #[DeleteMapping("/marco")]
    function delete_with_path()
    {
        echo "Prefixed Polo";
    }

    #[DeleteMapping("/code/{code}/number/{number}")]
    function delete_with_path_vars(string $code, int $number)
    {
        echo "Prefixed Code: $code | Number: $number";
    }

    #[DeleteMapping("/throw_illegal_arg_exception")]
    function delete_that_throws()
    {
        throw new IllegalArgumentException("Illegal");
    }

    #[ExceptionHandler(IllegalArgumentException::class)]
    function handle_illegal_arg_exc(IllegalArgumentException $exc, string $path) {
        echo "Prefixed Swallowed ". $_SERVER['REQUEST_METHOD'] . " exception: " . $exc->getMessage()  . " for path: $path";
    }

    #[GetMapping("/global_exception")]
    function get_global_exception() {
        throw new UnexpectedValueException("Unexpected");
    }

}
