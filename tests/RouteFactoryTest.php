<?php

include_once __DIR__ . '/../vendor/autoload.php';

use FT\Routing\Exceptions\RouteAlreadyExistsException;
use FT\Routing\RouteFactory;

foreach ([
    'BaseTest',
    'GoodController',
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
    }
}

?>