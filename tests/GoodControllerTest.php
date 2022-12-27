<?php

include __DIR__ . '/../vendor/autoload.php';

use FT\Routing\RouteFactory;

foreach ([
    'BaseTest',
    'GoodController'
] as $c) {
    include_once __DIR__ . "/./$c.php";
}

final class GoodControllerTest extends BaseTest {

    /**
    * @test
    * @dataProvider good_requests
    */
    public function should_route_test($req_method, $path, $expected_out, array $headers = []) {
        $this->setup_server($req_method, $path, $headers);

        RouteFactory::registerController(GoodController::class);
        RouteFactory::onNotFound(function () { });
        RouteFactory::dispatch();

        $this->expectOutputString($expected_out);
    }

    public function good_requests() {
        return [
            ['GET', '/good', "Hello World"],
            ['GET', '/good/', "Hello World"],
            ['GET', '/good/marco', 'Polo'],
            ['GET', '/good/code/foobar/number/12345', "Code: foobar | Number: 12345"],
            ['GET', '/good/throw_illegal_arg_exception', "Swallowed GET exception: Illegal for path: /good/throw_illegal_arg_exception"],
            ['POST', '/good', "Hello World"],
            ['POST', '/good/', "Hello World"],
            ['POST', '/good/marco', 'Polo'],
            ['POST', '/good/code/foobar/number/12345', "Code: foobar | Number: 12345"],
            ['POST', '/good/throw_illegal_arg_exception', "Swallowed POST exception: Illegal for path: /good/throw_illegal_arg_exception"],
            ['PUT', '/good', "Hello World"],
            ['PUT', '/good/', "Hello World"],
            ['PUT', '/good/marco', 'Polo'],
            ['PUT', '/good/code/foobar/number/12345', "Code: foobar | Number: 12345"],
            ['PUT', '/good/throw_illegal_arg_exception', "Swallowed PUT exception: Illegal for path: /good/throw_illegal_arg_exception"],
            ['DELETE', '/good', "Hello World"],
            ['DELETE', '/good/', "Hello World"],
            ['DELETE', '/good/marco', 'Polo'],
            ['DELETE', '/good/code/foobar/number/12345', "Code: foobar | Number: 12345"],
            ['DELETE', '/good/throw_illegal_arg_exception', "Swallowed DELETE exception: Illegal for path: /good/throw_illegal_arg_exception"],

            ['GET', "/good/reqmap", "Hello from GET"],
            ['HEAD', "/good/reqmap", "Hello from HEAD"],
            ['TRACE', "/good/reqmap", "Hello from TRACE"],

            ['GET', '/good/reqparam', "Request param result: "],
            ['GET', '/good/reqparam?foo=abc', "Request param result: abc"],
            ['GET', '/good/reqparam/array?foo[]=abc&foo[]=123', "Request param result: abc, 123"],
            ['GET', '/good/code/my-code/id/99/reqparam/many?bar=buzz&foo[]=abc&foo[]=123&name=john%20doe', "Code: my-code | Number: 99 | Request param result: abc, 123, buzz, john doe"],

            ['GET', '/good/reqheader', "Request header result: accept => text/*;q=0.3, application/json;q=0.7, */*;q=0.2", [
                'HTTP_ACCEPT' => 'text/*;q=0.3, application/json;q=0.7, */*;q=0.2'
            ]],

            ['GET', '/good/code/my-code/id/99/reqheader/many', "Code: my-code | Number: 99 | Request header result: referer => referred-by-x, accept => text/*;q=0.3, application/json;q=0.7, */*;q=0.2", [
                'HTTP_ACCEPT' => 'text/*;q=0.3, application/json;q=0.7, */*;q=0.2',
                'HTTP_REFERER' => 'referred-by-x'
            ]],

        ];
    }

}

?>