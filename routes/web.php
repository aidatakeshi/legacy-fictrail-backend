<?php

$router->get('/', function () use ($router) {
    return $router->app->version();
});

/**
 * User Account
 */

$router->post('login', ['uses' => 'UserAccountController@login']);

$router->group(['middleware' => 'auth'], function ($router) {

    $router->get('myself', ['uses' => 'UserAccountController@getMyself']);
    $router->post('logout', ['uses' => 'UserAccountController@logout']);
    $router->post('change-password', ['uses' => 'UserAccountController@changePassword']);

});

/**
 * File Upload
 */

$router->get('file/{uuid}', ['uses' => 'FileController@getFile']);

$router->group(['middleware' => 'auth'], function ($router) {

    $router->post('file', ['uses' => 'FileController@postFile']);
    $router->delete('file/{uuid}', ['uses' => 'FileController@deleteFile']);

});