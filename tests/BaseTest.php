<?php

use FT\Routing\RouteFactory;
use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase {

    protected function setup_server($req_method, $path, $headers = [])
    {
        $_SERVER['SERVER_PROTOCOL'] = 'http';
        $_SERVER['SERVER_NAME'] = 'localhost';
        $_SERVER['REQUEST_METHOD'] = strtoupper($req_method);
        $_SERVER['SERVER_PORT'] = '8080';
        $_SERVER['REQUEST_URI'] = $path;
        $_SERVER['HTTP_ACCEPT'] = "text/*;q=0.3, application/json;q=0.7, */*;q=0.2";

        foreach ($headers as $key => $value)
            $_SERVER[$key] = $value;
    }

    protected function setUp(): void
    {
        $cls = new ReflectionClass(RouteFactory::class);
        $cls->setStaticPropertyValue("controllers", []);
        $cls->setStaticPropertyValue("not_found_handler", null);
        $cls->setStaticPropertyValue("throwable_handlers", []);
        $cls->setStaticPropertyValue("middleware_handlers", []);
    }

}

?>