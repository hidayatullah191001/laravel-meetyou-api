<?php

use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\UserProfileController;
use App\Http\Controllers\API\GalleryController;
use App\Http\Controllers\API\MatchUserController;
use App\Http\Controllers\API\SocialMediaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function(){
    Route::get('user', [UserController::class, 'fetch']);
    Route::get('user/all', [UserController::class, 'user_to_match_all']);
    Route::post('logout', [UserController::class, 'logout']);
    Route::post('user/{id}', [UserController::class, 'update']);

    // User Profile
    Route::post('profile', [UserProfileController::class, 'store']);
    Route::get('profile', [UserProfileController::class, 'all']);
    Route::post('profile/{id}', [UserProfileController::class, 'update']);
    Route::delete('profile/{id}', [UserProfileController::class, 'destroy']);

    // Route Gallery
    Route::get('gallery', [GalleryController::class, 'all']);
    Route::post('gallery', [GalleryController::class, 'store']);
    Route::delete('gallery/{id}', [GalleryController::class, 'destroy']);

    // Route Social Media
    Route::post('social-media', [SocialMediaController::class, 'store']);
    Route::get('social-media', [SocialMediaController::class, 'all']);
    Route::post('social-media/{id}', [SocialMediaController::class, 'update']);
    Route::delete('social-media/{id}', [SocialMediaController::class, 'destroy']);

    // Route Match User
    Route::get('match', [MatchUserController::class,'all']);
    Route::post('match', [MatchUserController::class, 'store']);
    Route::delete('match/{id}', [MatchUserController::class,'destroy']);
});

Route::get('matches', [MatchUserController::class, 'all_public']);


Route::post('login',[UserController::class, 'login']);
Route::post('register',[UserController::class, 'register']);
