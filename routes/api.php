<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use Dingo\Api\Routing\Router;

/** @var Router $api */
$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function (Dingo\Api\Routing\Router $api) {
    $api->post('/auth/login', [AuthController::class, 'login']);
    $api->group(
        [
            'middleware' => ['api.throttle', 'api.auth', 'bindings'],
            'limit' => 10,
            'expires' => 1
        ],
        function (Dingo\Api\Routing\Router $api) {
            $api->post('/auth/logout', [AuthController::class, 'logout']);

            // resource products
            $api->resource("products", ProductController::class, [
                "only" => ["index", "show", "store", "update", "destroy"]
            ]);
        }
    );
});

