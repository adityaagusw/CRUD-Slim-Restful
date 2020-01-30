<?php

use Slim\App;

return function (App $app) {
    // e.g: $app->add(new \Slim\Csrf\Guard);

    $app->add(new Tuupola\Middleware\HttpBasicAuthentication([
    "secure"=>false,
    "users" => [
        "adityaagusw" => "123456",
        ]
    ]));

};
