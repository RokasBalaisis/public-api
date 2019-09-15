<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});




    $router->post('login', ['uses' => 'AuthController@login'], function (){});
    $router->post('reissue', ['uses' => 'AuthController@reissueToken'], function (){});

    $router->group(['prefix' => 'users'], function () use ($router) {

        $router->group(['middleware' => 'jwt.auth'], function () use ($router) {
            $router->get('/', ['uses' => 'UserController@index'], function (){});
            $router->get('/{id}', ['uses' => 'UserController@show'], function (){});
            $router->post('/', ['uses' => 'UserController@store'], function (){});
            $router->put('/{id}', ['uses' => 'UserController@update'], function (){});
            $router->delete('/{id}', ['uses' => 'UserController@destroy'], function (){});
        });

        
    });

    $router->group(['prefix' => 'roles'], function () use ($router) {

        $router->group(['middleware' => 'jwt.auth'], function () use ($router) {
            $router->get('/', ['uses' => 'RoleController@index'], function (){});
            // $router->get('/{id}', ['uses' => 'UserController@show'], function (){});
            // $router->post('/', ['uses' => 'UserController@store'], function (){});
            // $router->put('/{id}', ['uses' => 'UserController@update'], function (){});
            // $router->delete('/{id}', ['uses' => 'UserController@destroy'], function (){});
        });

        
    });
    
