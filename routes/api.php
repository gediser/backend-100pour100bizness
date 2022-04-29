<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicationController;
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

Route::middleware('auth:sanctum')->group(function(){
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::resource('/publication', PublicationController::class);

    Route::resource('/product', ProductController::class);

    Route::post('/update-profil', [AuthController::class, 'updateProfil']);
});

Route::get('/categories', [ProductController::class, 'categories']);

Route::get('/seed/categories', [ProductController::class, 'seedCategories']);

Route::get('/view/publication', [PublicationController::class, 'viewPublicPublications']);

Route::get('/view/product', [ProductController::class, 'viewPublicProducts']);

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);