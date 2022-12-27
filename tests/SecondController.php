<?php

use FT\Routing\Attributes\GetMapping;
use FT\Routing\Attributes\RequestMapping;

#[RequestMapping(value: "/good")]
final class SecondController {

    #[GetMapping]
    function get_no_path()
    {
        echo "Hello World";
    }

}

#[RequestMapping(value: "/good/reqmap")]
final class ThirdController {

}

#[RequestMapping(value: "/good")]
final class AgainstPrefixController {

    #[GetMapping(value: "/foo/bazz/buzz")]
    public function get_buzz() {}

}

?>