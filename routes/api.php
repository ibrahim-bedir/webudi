<?php

use App\Http\Controllers\ArtistController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\UserController;
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

Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('artists', [ArtistController::class, 'index']);
    Route::get('artists/{artist}/tracks', [ArtistController::class, 'artistTracks']);
    Route::get('genres', [GenreController::class, 'index']);
    Route::get('genres/{genre:slug}/tracks', [GenreController::class, 'genreTracks']);
    Route::get('genres/{genre:slug}/artists', [GenreController::class, 'genreArtists']);

    // user routes
    Route::get('my-profile', [UserController::class, 'myProfile']);
    Route::post('user/photo', [UserController::class, 'changePhoto']);
});
