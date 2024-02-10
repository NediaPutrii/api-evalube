<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/posts', function(){
    dd('INI API EVALUBE EA');
});

Route::get('/users', [UserController::class, 'index']);

;

$router->group(['prefix' => 'auth'], function() use($router){
    $router->post('/register', 'AuthController@register');
    $router->post('/login', 'AuthController@login');
});

$router->group(['middleware' => 'auth'], function($router){
    //logout
    $router->post('/logout', 'AuthController@logout');
    $router->get('/myprofile', 'UserController@show');

    

});