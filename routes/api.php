<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\HomeController;

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

    Route::get('/users/search', [SearchController::class, 'usersSearch']);

    Route::post('/users/make/admin', [SearchController::class, 'usersMakeAdmin']);
    Route::post('/users/make/user', [SearchController::class, 'usersMakeUser']);
    
    Route::get('/publications/get', [PublicationController::class, 'publicationsGet']);
    Route::post('/juste-pour-vous-publications/save', [PublicationController::class, 'publicationsJusteSave']);
    Route::get('/juste-pour-vous-publications/get-all', [PublicationController::class, 'publicationsJusteGetAll']);
    Route::post('/meilleur-classement/save', [PublicationController::class, 'publicationsMeilleurSave']);
    Route::get('/meilleur-classement/get-all', [PublicationController::class, 'publicationsMeilleurGetAll']);
});

Route::post('/search/category/{id}', [SearchController::class, 'category']);

Route::post('/search/all', [SearchController::class, 'all']);

Route::get('/home/data', [HomeController::class, 'homeData']);

Route::get('/categories', [ProductController::class, 'categories']);

Route::get('/seed/categories', [ProductController::class, 'seedCategories']);

Route::get('/view/publication', [PublicationController::class, 'viewPublicPublications']);

Route::get('/view/product', [ProductController::class, 'viewPublicProducts']);

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);