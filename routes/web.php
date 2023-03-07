<?php

use Laravel\Lumen\Routing\Router;

/** @var Router $router */
$router->get('/', 'HomeController@home');

$router->group(['prefix' => 'v1'], function (Router $router) {
    $router->post('epgs', 'EpgController@post');
});
