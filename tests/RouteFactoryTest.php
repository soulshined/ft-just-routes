<?php

include_once __DIR__ . '/../vendor/autoload.php';

use FT\Routing\Attributes\GetMapping;
use FT\Routing\Attributes\RequestMapping;
use FT\Routing\Exceptions\RouteAlreadyExistsException;
use FT\Routing\Exceptions\RouteException;
use FT\Routing\RouteFactory;

foreach ([
    'BaseTest',
    'GoodController',
    'GoodControllerWithPrefix',
    'SecondController'
] as $c) {
    include_once __DIR__ . "/./$c.php";
}

final class RouteFactoryTest extends BaseTest {

    /**
    * @test
    */
    public function should_handle_not_found() {
        $this->setup_server('GET', "/some/path/doesnt/exist");

        RouteFactory::registerController(GoodController::class);
        RouteFactory::onNotFound(function ($path) {
            echo "$path not found";
        });
        RouteFactory::dispatch();

        $this->expectOutputString("/some/path/doesnt/exist not found");
    }

    /**
     * @test
     */
    public function should_handle_global_exception() {
        $this->setup_server('GET', '/good/global_exception');

        RouteFactory::registerController(GoodController::class);
        RouteFactory::onException(UnexpectedValueException::class, function () {
            echo "Caught globally";
        } );
        RouteFactory::onNotFound(function($path) {
            $this->fail("$path not found");
        });
        RouteFactory::dispatch();

        $this->expectOutputString("Caught globally");
    }

    /**
     * @test
     */
    public function middleware_test()
    {
        $this->setup_server('GET', '/good');

        RouteFactory::registerController(GoodController::class);
        RouteFactory::beforeEach(function ($path) {
            echo "Hello from middleware";
        });
        RouteFactory::onNotFound(function ($path) {
            $this->fail("$path not found");
        });
        RouteFactory::dispatch();

        $this->expectOutputString("Hello from middlewareHello World");
    }

    /**
     * @test
     */
    public function multiple_controllers_test() {
        $this->setup_server('GET', '/good');

        $this->expectException(RouteAlreadyExistsException::class);
        $this->expectExceptionMessage("Route SecondController::/good already exists on GoodController");

        RouteFactory::registerController(
            GoodController::class,
            SecondController::class,
            ThirdController::class
        );

        RouteFactory::onNotFound(function () {
            $this->fail();
        });
    }

    /**
     * @test
     */
    public function multiple_controllers_with_prefix_test() {
        $this->setup_server('GET', '/good/foo/bazz/buzz');
        $this->expectException(RouteAlreadyExistsException::class);
        $this->expectExceptionMessage("Route AgainstPrefixController::/good/foo/bazz/buzz already exists on GoodControllerWithPrefix");

        RouteFactory::registerController(
            GoodControllerWithPrefix::class,
            AgainstPrefixController::class
        );

        RouteFactory::onNotFound(function () {
            $this->fail();
        });
    }

    /**
    * @test
    */
    public function should_throw_for_placeholders_in_controller_request_mapping_test() {
        $this->setup_server('GET', '/good/foo/bazz/buzz');
        $this->expectException(RouteException::class);
        $this->expectExceptionMessage("Controller #[RequestMapping] can not contain path variable placeholders @ FooController");

        RouteFactory::registerController(FooController::class);

        RouteFactory::onNotFound(function () {
            $this->fail();
        });
    }
}

#[RequestMapping(value: "/foobar/name/{name}/number/{number}")]
final class FooController {

    #[GetMapping]
    private function get() {}

}

?>