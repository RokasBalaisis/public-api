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



    $router->get('/noimage', ['uses' => 'MediaController@noFileImage'], function (){});

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

        $router->group(['middleware' => ['auth:api', 'role:admin']], function () use ($router) {
            $router->get('/', ['uses' => 'RoleController@index'], function (){});
            $router->get('/{id}', ['uses' => 'RoleController@show'], function (){});
            $router->post('/', ['uses' => 'RoleController@store'], function (){});
            $router->put('/{id}', ['uses' => 'RoleController@update'], function (){});
            $router->delete('/{id}', ['uses' => 'RoleController@destroy'], function (){});
        });

        
    });

    $router->group(['prefix' => 'media'], function () use ($router) {

       $router->group(['middleware' => ['auth:api', 'role:admin']], function () use ($router) {
            $router->post('/', ['uses' => 'MediaController@store'], function (){});
            $router->put('/{id}', ['uses' => 'MediaController@update'], function (){});
            $router->delete('/{id}', ['uses' => 'MediaController@destroy'], function (){});
                
       });
        $router->get('/', ['uses' => 'MediaController@index'], function (){});
        $router->get('/{id}', ['uses' => 'MediaController@show'], function (){});
        $router->get('/file/{id}', ['uses' => 'MediaController@downloadFile'], function (){});
        $router->get('/name/{media_file_name}', ['uses' => 'MediaController@getMediaByName'], function (){});
    });

    $router->group(['prefix' => 'actors'], function () use ($router) {

        $router->group(['middleware' => ['auth:api', 'role:admin']], function () use ($router) {
            $router->get('/', ['uses' => 'ActorController@index'], function (){});
            $router->get('/{id}', ['uses' => 'ActorController@show'], function (){});
            $router->post('/', ['uses' => 'ActorController@store'], function (){});
            $router->put('/{id}', ['uses' => 'ActorController@update'], function (){});
            $router->delete('/{id}', ['uses' => 'ActorController@destroy'], function (){});
        });

        

        
    });

    $router->group(['prefix' => 'categories'], function () use ($router) {

        $router->group(['middleware' => ['auth:api', 'role:admin']], function () use ($router) {
            $router->post('/', ['uses' => 'CategoryController@store'], function (){});
            $router->put('/{id}', ['uses' => 'CategoryController@update'], function (){});
            $router->delete('/{id}', ['uses' => 'CategoryController@destroy'], function (){});
        });

        $router->get('/', ['uses' => 'CategoryController@index'], function (){});
        $router->get('/{id}', ['uses' => 'CategoryController@show'], function (){});

        
    });

    $router->group(['prefix' => 'mediatypes'], function () use ($router) {

        $router->group(['middleware' => ['auth:api', 'role:admin']], function () use ($router) {
            $router->post('/', ['uses' => 'MediaTypeController@store'], function (){});
            $router->put('/{id}', ['uses' => 'MediaTypeController@update'], function (){});
            $router->delete('/{id}', ['uses' => 'MediaTypeController@destroy'], function (){});
        });

        $router->get('/', ['uses' => 'MediaTypeController@index'], function (){});
        $router->get('/media', ['uses' => 'MediaTypeController@indexMedia'], function (){});
        $router->get('/media/latest', ['uses' => 'MediaTypeController@indexMediaLatest'], function (){});
        $router->get('/media/count', ['uses' => 'MediaTypeController@count'], function (){});
        $router->get('/{id}', ['uses' => 'MediaTypeController@show'], function (){});
        $router->get('/{id}/media', ['uses' => 'MediaTypeController@showMedia'], function (){});
        
    });

    $router->group(['prefix' => 'comments'], function () use ($router) {

        $router->group(['middleware' => ['auth:api', 'role:admin']], function () use ($router) {
            $router->get('/', ['uses' => 'CommentController@index'], function (){});
            $router->get('/{id}', ['uses' => 'CommentController@show'], function (){});     
            $router->put('/{id}', ['uses' => 'CommentController@update'], function (){});
            $router->delete('/{id}', ['uses' => 'CommentController@destroy'], function (){});
        });
        $router->group(['middleware' => 'auth:api', 'role:admin,user'], function () use ($router) {
        $router->post('/', ['uses' => 'CommentController@store'], function (){});
        });
        
    });

    $router->group(['prefix' => 'ratings'], function () use ($router) {

        $router->group(['middleware' => ['auth:api', 'role:admin']], function () use ($router) {
            $router->get('/', ['uses' => 'RatingController@index'], function (){});
            $router->get('/{id}', ['uses' => 'RatingController@show'], function (){});    
            $router->put('/{id}', ['uses' => 'RatingController@update'], function (){});
            $router->delete('/{id}', ['uses' => 'RatingController@destroy'], function (){});
        });
        $router->group(['middleware' => ['auth:api', 'role:admin,user']], function () use ($router) {
        $router->post('/', ['uses' => 'RatingController@store'], function (){});
        });
        
    });
    
    
