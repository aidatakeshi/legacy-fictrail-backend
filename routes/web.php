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

/**
 * General Resource Access
 */

$router->get('items/{type}', ['uses' => 'GeneralResourceController@getItems']);
$router->get('items/{type}/{id}', ['uses' => 'GeneralResourceController@getItem']);

$router->group(['middleware' => 'auth'], function ($router) {
    $router->post('items/{type}', ['uses' => 'GeneralResourceController@createItem']);
    $router->post('items/{type}/{id}', ['uses' => 'GeneralResourceController@duplicateItem']);
    $router->patch('items/{type}/{id}', ['uses' => 'GeneralResourceController@updateItem']);
    $router->delete('items/{type}/{id}', ['uses' => 'GeneralResourceController@removeItem']);
});

//Aliases
$router->get('i/{type}', ['uses' => 'GeneralResourceController@getItems']);
$router->get('i/{type}/{id}', ['uses' => 'GeneralResourceController@getItem']);

$router->group(['middleware' => 'auth'], function ($router) {
    $router->post('i/{type}', ['uses' => 'GeneralResourceController@createItem']);
    $router->post('i/{type}/{id}', ['uses' => 'GeneralResourceController@duplicateItem']);
    $router->patch('i/{type}/{id}', ['uses' => 'GeneralResourceController@updateItem']);
    $router->delete('i/{type}/{id}', ['uses' => 'GeneralResourceController@removeItem']);
});

/**
 * Lines / Stations
 */
$router->get('line-stats-hr', ['uses' => 'LineStationController@getLineStatsHR']);
$router->get('line/{line_id}/stations', ['uses' => 'LineStationController@getStationsOfLine']);
$router->get('station/{station_id}/lines', ['uses' => 'LineStationController@getLinesOfStation']);
$router->post('station/get-by-ids', ['uses' => 'LineStationController@getStationByIDs']);

$router->get('line/{line_id}/name', ['uses' => 'LineStationController@getLineName']);
$router->get('station/{station_id}/name', ['uses' => 'LineStationController@getStationName']);

/**
 * Schedule Template
 */
$router->post('schdraft-editor/sch-template-info', ['uses' => 'SchDraftController@provideSchTemplateInfo']);
$router->get('schdraft-template/{id}/sch-output', ['uses' => 'SchDraftController@getSchTemplateOutput']);