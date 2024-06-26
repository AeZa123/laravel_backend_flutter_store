<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;

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


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Route::middleware('custom_auth')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::resource('product', ProductController::class);

Route::Group(['middleware' => 'custom_auth'], function(){
    
    // get all users
    Route::get('users', [UserController::class, 'index']);
    
    // crud product
    Route::resource('product', ProductController::class);





    // Route::resource('products', ProductController::class);
    // Route::get('products/search/{keyword}', [ProductController::class, 'search']);
    Route::post('logout', [AuthController::class, 'logout']);
    // Route::post('logout', [AuthController::class, 'logout']);

});
// Route::Group(['middleware' => 'auth:sanctum'], function(){
    
//     // get all users
//     Route::get('users', [UserController::class, 'index']);
    
//     // crud product
//     Route::resource('product', ProductController::class);





//     // Route::resource('products', ProductController::class);
//     // Route::get('products/search/{keyword}', [ProductController::class, 'search']);
//     Route::post('logout', [AuthController::class, 'logout']);
//     // Route::post('logout', [AuthController::class, 'logout']);

// });