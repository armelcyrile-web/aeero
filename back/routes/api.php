<?php

use App\Http\Controllers\Api\AlbumController;
use App\Http\Controllers\Api\AncienPresidentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\MembreBureauController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\Api\PublicationController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/newsletter', [NewsletterController::class, 'store']);
Route::post('/contact', [ContactController::class, 'send']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});

Route::get('/publications', [PublicationController::class, 'index']);
Route::get('/publications/{id}', [PublicationController::class, 'show']);

Route::get('/albums', [AlbumController::class, 'index']);
Route::get('/albums/{id}', [AlbumController::class, 'show']);

Route::get('/membres-bureau', [MembreBureauController::class, 'index']);

Route::get('/anciens-presidents', [AncienPresidentController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/publications', [PublicationController::class, 'store']);
    Route::put('/publications/{id}', [PublicationController::class, 'update']);
    Route::delete('/publications/{id}', [PublicationController::class, 'destroy']);

    Route::post('/albums', [AlbumController::class, 'store']);
    Route::put('/albums/{id}', [AlbumController::class, 'update']);
    Route::delete('/albums/{id}', [AlbumController::class, 'destroy']);
    Route::post('/albums/{albumId}/photos', [AlbumController::class, 'addPhotos']);

    Route::post('/membres-bureau', [MembreBureauController::class, 'store']);
    Route::put('/membres-bureau/{id}', [MembreBureauController::class, 'update']);
    Route::delete('/membres-bureau/{id}', [MembreBureauController::class, 'destroy']);

    Route::post('/anciens-presidents', [AncienPresidentController::class, 'store']);
    Route::put('/anciens-presidents/{id}', [AncienPresidentController::class, 'update']);
    Route::delete('/anciens-presidents/{id}', [AncienPresidentController::class, 'destroy']);
});
