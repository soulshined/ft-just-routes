<?php

include __DIR__ . '/../vendor/autoload.php';

use FT\Routing\RouteFactory;

foreach ([
    'BaseTest',
    'GoodControllerWithPrefix'
] as $c) {
    include_once __DIR__ . "/./$c.php";
}

final class GoodControllerWithPrefixTest extends BaseTest {

    /**
    * @test
    * @dataProvider good_requests
    */
    public function should_route_test($req_method, $path, $expected_out) {
        $this->setup_server($req_method, $path);

        RouteFactory::registerController(GoodControllerWithPrefix::class);
        RouteFactory::onNotFound(function () {});
        RouteFactory::dispatch();

        $this->expectOutputString($expected_out);
    }


    public function good_requests() {
        return [
            ['GET', '/good/foo/bazz/buzz', "Prefixed Hello World"],
            ['GET', '/good/foo/bazz/buzz/', "Prefixed Hello World"],
            ['GET', '/good/foo/bazz/buzz/marco', 'Prefixed Polo'],
            ['GET', '/good/foo/bazz/buzz/code/foobar/number/12345', "Prefixed Code: foobar | Number: 12345"],
            ['GET', '/good/foo/bazz/buzz/throw_illegal_arg_exception', "Prefixed Swallowed GET exception: Illegal for path: /good/foo/bazz/buzz/throw_illegal_arg_exception"],
            ['POST', '/good/foo/bazz/buzz', "Prefixed Hello World"],
            ['POST', '/good/foo/bazz/buzz/', "Prefixed Hello World"],
            ['POST', '/good/foo/bazz/buzz/marco', 'Prefixed Polo'],
            ['POST', '/good/foo/bazz/buzz/code/foobar/number/12345', "Prefixed Code: foobar | Number: 12345"],
            ['POST', '/good/foo/bazz/buzz/throw_illegal_arg_exception', "Prefixed Swallowed POST exception: Illegal for path: /good/foo/bazz/buzz/throw_illegal_arg_exception"],
            ['PUT', '/good/foo/bazz/buzz', "Prefixed Hello World"],
            ['PUT', '/good/foo/bazz/buzz/', "Prefixed Hello World"],
            ['PUT', '/good/foo/bazz/buzz/marco', 'Prefixed Polo'],
            ['PUT', '/good/foo/bazz/buzz/code/foobar/number/12345', "Prefixed Code: foobar | Number: 12345"],
            ['PUT', '/good/foo/bazz/buzz/throw_illegal_arg_exception', "Prefixed Swallowed PUT exception: Illegal for path: /good/foo/bazz/buzz/throw_illegal_arg_exception"],
            ['DELETE', '/good/foo/bazz/buzz', "Prefixed Hello World"],
            ['DELETE', '/good/foo/bazz/buzz/', "Prefixed Hello World"],
            ['DELETE', '/good/foo/bazz/buzz/marco', 'Prefixed Polo'],
            ['DELETE', '/good/foo/bazz/buzz/code/foobar/number/12345', "Prefixed Code: foobar | Number: 12345"],
            ['DELETE', '/good/foo/bazz/buzz/throw_illegal_arg_exception', "Prefixed Swallowed DELETE exception: Illegal for path: /good/foo/bazz/buzz/throw_illegal_arg_exception"],

            ['GET', "/good/foo/bazz/buzz/reqmap", "Prefixed Hello from GET"],
            ['HEAD', "/good/foo/bazz/buzz/reqmap", "Prefixed Hello from HEAD"],
            ['TRACE', "/good/foo/bazz/buzz/reqmap", "Prefixed Hello from TRACE"]
        ];
    }

}

?>