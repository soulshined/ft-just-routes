<?php

use FT\RequestResponse\Enums\RequestMethods;
use FT\Routing\Attributes\RequestMapping;

#[RequestMapping(value: "/good")]
final class SecondController {

}

#[RequestMapping(value: "/good/reqmap")]
final class ThirdController {

}

?>