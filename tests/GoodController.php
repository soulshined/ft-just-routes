<?php

use FT\Attributes\Validation\IllegalArgumentException;
use FT\RequestResponse\Enums\RequestMethods;
use FT\Routing\Attributes\DeleteMapping;
use FT\Routing\Attributes\ExceptionHandler;
use FT\Routing\Attributes\GetMapping;
use FT\Routing\Attributes\PostMapping;
use FT\Routing\Attributes\PutMapping;
use FT\Routing\Attributes\RequestHeader;
use FT\Routing\Attributes\RequestMapping;
use FT\Routing\Attributes\RequestParam;

include __DIR__ . '/../vendor/autoload.php';

#[RequestMapping(value: "/good")]
final class GoodController {

    #[GetMapping]
    function get_no_path() {
        echo "Hello World";
    }

    #[RequestMapping(value: "/reqmap", methods: [RequestMethods::GET, RequestMethods::HEAD, RequestMethods::TRACE])]
    function request_mapping() {
        echo "Hello from " . $_SERVER['REQUEST_METHOD'];
    }

    #[GetMapping(value: "/marco")]
    function get_with_path() {
        echo "Polo";
    }

    #[GetMapping(value: "/code/{code}/number/{number}")]
    function get_with_path_vars(string $code, int $number) {
        echo "Code: $code | Number: $number";
    }

    #[GetMapping(value: "/throw_illegal_arg_exception")]
    function get_that_throws() {
        throw new IllegalArgumentException("Illegal");
    }

    #[PostMapping]
    function post_no_path()
    {
        echo "Hello World";
    }

    #[PostMapping(value: "/marco")]
    function post_with_path()
    {
        echo "Polo";
    }

    #[PostMapping(value: "/code/{code}/number/{number}")]
    function post_with_path_vars(string $code, int $number)
    {
        echo "Code: $code | Number: $number";
    }

    #[PostMapping(value: "/throw_illegal_arg_exception")]
    function post_that_throws()
    {
        throw new IllegalArgumentException("Illegal");
    }

    #[PutMapping]
    function put_no_path()
    {
        echo "Hello World";
    }

    #[PutMapping(value: "/marco")]
    function put_with_path()
    {
        echo "Polo";
    }

    #[PutMapping(value: "/code/{code}/number/{number}")]
    function put_with_path_vars(string $code, int $number)
    {
        echo "Code: $code | Number: $number";
    }

    #[PutMapping(value: "/throw_illegal_arg_exception")]
    function put_that_throws()
    {
        throw new IllegalArgumentException("Illegal");
    }

    #[DeleteMapping]
    function delete_no_path()
    {
        echo "Hello World";
    }

    #[DeleteMapping(value: "/marco")]
    function delete_with_path()
    {
        echo "Polo";
    }

    #[DeleteMapping(value: "/code/{code}/number/{number}")]
    function delete_with_path_vars(string $code, int $number)
    {
        echo "Code: $code | Number: $number";
    }

    #[DeleteMapping(value: "/throw_illegal_arg_exception")]
    function delete_that_throws()
    {
        throw new IllegalArgumentException("Illegal");
    }

    #[GetMapping(value: "/reqheader")]
    function get_with_request_header(#[RequestHeader] string $accept) {
        echo "Request header result: accept => $accept";
    }

    #[GetMapping(value: "/code/{code}/id/{id}/reqheader/many")]
    function get_with_many_request_header(int $id, string $code, #[RequestHeader] string $referer, #[RequestHeader] string $accept) {
        echo "Code: $code | Number: $id | Request header result: referer => $referer, accept => $accept";
    }

    #[GetMapping(value: "/reqparam")]
    function get_with_request_param(#[RequestParam] string $foo) {
        echo "Request param result: $foo";
    }

    #[GetMapping(value: "/reqparam/array")]
    function get_with_request_param_array(#[RequestParam] array $foo) {
        echo "Request param result: " . join(", ", $foo);
    }

    #[GetMapping(value: "/code/{code}/id/{id}/reqparam/many")]
    function get_with_many_request_param(
        int $id,
        string $code,
        #[RequestParam] array $foo,
        #[RequestParam] string $bar,
        #[RequestParam('name')] string $userName
    ) {
        echo "Code: $code | Number: $id | Request param result: " . join(", ", $foo) . ", $bar, $userName";
    }

    #[ExceptionHandler(IllegalArgumentException::class)]
    function handle_illegal_arg_exc(IllegalArgumentException $exc, string $path) {
        echo "Swallowed ". $_SERVER['REQUEST_METHOD'] . " exception: " . $exc->getMessage()  . " for path: $path";
    }

    #[GetMapping(value: "/global_exception")]
    function get_global_exception() {
        throw new UnexpectedValueException("Unexpected");
    }

}

?>