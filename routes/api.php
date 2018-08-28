<?php

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

Route::any('{class}/{action}', function ($class, $action) {

    $ctrl = App::make("\\App\\Http\\Controllers\\Api\\" . ucfirst(strtolower($class)) . "Controller");

    return App::call([$ctrl, strtolower($action) . 'Action']);
});