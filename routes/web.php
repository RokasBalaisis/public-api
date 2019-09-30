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
    $router->post('register', ['uses' => 'AuthController@register'], function (){});

    $router->group(['middleware' => 'auth:api'], function () use ($router) {
        $router->post('logout', ['uses' => 'AuthController@logout'], function (){});
    });
    

    $router->group(['prefix' => 'users'], function () use ($router) {

        $router->group(['middleware' => ['auth:api', 'role:admin']], function () use ($router) {
            $router->get('/', ['uses' => 'UserController@index'], function (){});
            $router->get('/{id}', ['uses' => 'UserController@show'], function (){});
            $router->post('/', ['uses' => 'UserController@store'], function (){});
            $router->put('/{id}', ['uses' => 'UserController@update'], function (){});
            $router->delete('/{id}', ['uses' => 'UserController@destroy'], function (){});
        });

        
    });

    $router->group(['prefix' => 'roles'], function () use ($router) {

        $router->group(['middleware' => 'auth:api', 'role:admin'], function () use ($router) {
            $router->get('/', ['uses' => 'RoleController@index'], function (){});
            $router->get('/{id}', ['uses' => 'RoleController@show'], function (){});
            $router->post('/', ['uses' => 'RoleController@store'], function (){});
            $router->put('/{id}', ['uses' => 'RoleController@update'], function (){});
            $router->delete('/{id}', ['uses' => 'RoleController@destroy'], function (){});
        });

        
    });

    $router->group(['prefix' => 'media'], function () use ($router) {

        $router->group(['middleware' => 'auth:api', 'role:admin'], function () use ($router) {
            $router->get('/', ['uses' => 'MediaController@index'], function (){});
            $router->get('/{id}', ['uses' => 'MediaController@show'], function (){});
            $router->post('/', ['uses' => 'MediaController@store'], function (){});
            $router->put('/{id}', ['uses' => 'MediaController@update'], function (){});
            $router->delete('/{id}', ['uses' => 'MediaController@destroy'], function (){});
        });

        

        
    });

    // $router->group(['prefix' => 'actor'], function () use ($router) {

    //     $router->group(['middleware' => 'auth:api', 'role:admin'], function () use ($router) {
    //         $router->get('/', ['uses' => 'MediaController@index'], function (){});
    //         $router->get('/{id}', ['uses' => 'MediaController@show'], function (){});
    //         $router->post('/', ['uses' => 'MediaController@store'], function (){});
    //         $router->put('/{id}', ['uses' => 'MediaController@update'], function (){});
    //         $router->delete('/{id}', ['uses' => 'MediaController@destroy'], function (){});
    //     });

        

        
    // });

    // $router->group(['prefix' => 'category'], function () use ($router) {

    //     $router->group(['middleware' => 'auth:api', 'role:admin'], function () use ($router) {
    //         $router->get('/', ['uses' => 'MediaController@index'], function (){});
    //         $router->get('/{id}', ['uses' => 'MediaController@show'], function (){});
    //         $router->post('/', ['uses' => 'MediaController@store'], function (){});
    //         $router->put('/{id}', ['uses' => 'MediaController@update'], function (){});
    //         $router->delete('/{id}', ['uses' => 'MediaController@destroy'], function (){});
    //     });

        

        
    // });
    
    
