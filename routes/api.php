<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\UserController;
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

Route::group([
    'prefix' => 'auth',
], function() {
    Route::post('/login', [AuthController::class, 'login']);
});

Route::group([
    'middleware' => 'auth:api',
    'prefix' => '/users',
], function(){
    Route::post('/', [UserController::class, 'store']);
});

Route::group([
    'middleware' => 'auth:api',
    'prefix' => '/courses',
], function(){
    Route::post('/', [CourseController::class, 'store']);
    Route::delete('/{course}', [CourseController::class, 'delete'])->where('course', '[0-9]+');
    Route::group([
        'prefix' => '/{course}/registrations',
    ], function(){
        Route::post('/', [CourseController::class, 'register']);
    })->where('course', '[0-9]+');

    Route::group([
        'prefix' => '/{course}/posts',
    ], function(){
        Route::get('/', [PostController::class, 'listByUser']);
    })->where('course', '[0-9]+');
});

Route::group([
    'middleware' => 'auth:api',
    'prefix' => '/posts',
], function(){
    Route::post('/', [PostController::class, 'store']);
    Route::get('/', [PostController::class, 'list']);
    Route::put('/{post}', [PostController::class, 'edit'])->where('post', '[0-9]+');
    Route::delete('/{post}', [PostController::class, 'delete'])->where('post', '[0-9]+');

    Route::group([
        'prefix' => '/{post}/comments',
    ], function(){
        Route::post('/', [PostController::class, 'storeComment']);
        Route::delete('/{comment}', [PostController::class, 'deleteComment'])->where('comment', '[0-9]+');
    })->where('post', '[0-9]+');
});
