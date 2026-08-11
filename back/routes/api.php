<?php
// routes/api.php

declare(strict_types=1);

use App\Http\Controllers\Api\ActualiteController;
use App\Http\Controllers\Api\ActualitePubliqueController;
use App\Http\Controllers\Api\AlbumController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EvenementController;
use App\Http\Controllers\Api\EvenementPubliqueController;
use App\Http\Controllers\Api\MembreBureauController;
use App\Http\Controllers\Api\MembreBureauPubliqueController;
use App\Http\Controllers\Api\MessageContactController;
use App\Http\Controllers\Api\NewsletterAbonneController;
use App\Http\Controllers\Api\PartenaireController;
use App\Http\Controllers\Api\PartenairePubliqueController;
use App\Http\Controllers\Api\ProgrammeController;
use App\Http\Controllers\Api\ProgrammePubliqueController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AlbumPubliqueController;

// =============================================
// ROUTES PUBLIQUES (aucun middleware)
// =============================================

// Authentification
Route::post('/login', [AuthController::class, 'login']);

// Actualités
Route::get('/actualites', [ActualitePubliqueController::class, 'index']);
Route::get('/actualites/{slug}', [ActualitePubliqueController::class, 'show']);

// Événements
Route::get('/evenements', [EvenementPubliqueController::class, 'index']);
Route::get('/evenements/{slug}', [EvenementPubliqueController::class, 'show']);

// Programmes
Route::get('/programmes', [ProgrammePubliqueController::class, 'index']);
Route::get('/programmes/{slug}', [ProgrammePubliqueController::class, 'show']);

// Partenaires
Route::get('/partenaires', [PartenairePubliqueController::class, 'index']);

// Membres du bureau
Route::get('/membres-bureau', [MembreBureauPubliqueController::class, 'index']);

// Contact
Route::post('/messages-contact', [MessageContactController::class, 'store']);

// Newsletter
Route::post('/newsletter', [NewsletterAbonneController::class, 'store']);

// =============================================
// ROUTES PROTÉGÉES (auth:sanctum)
// =============================================

Route::middleware('auth:sanctum')->prefix('admin')->group(function (): void {

    // Authentification
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Actualités
    Route::apiResource('actualites', ActualiteController::class);
    Route::post('/actualites/{actualite}/submit', [ActualiteController::class, 'submit']);
    Route::post('/actualites/{actualite}/validate', [ActualiteController::class, 'validatePublication']);
    Route::post('/actualites/{actualite}/reject', [ActualiteController::class, 'reject']);

    // Galerie (albums publics)
    Route::get('/albums', [AlbumPubliqueController::class, 'index']);

    // Événements
    Route::apiResource('evenements', EvenementController::class);
    Route::post('/evenements/{evenement}/submit', [EvenementController::class, 'submit']);
    Route::post('/evenements/{evenement}/validate', [EvenementController::class, 'validatePublication']);
    Route::post('/evenements/{evenement}/reject', [EvenementController::class, 'reject']);

    // Programmes
    Route::apiResource('programmes', ProgrammeController::class);
    Route::post('/programmes/{programme}/submit', [ProgrammeController::class, 'submit']);
    Route::post('/programmes/{programme}/validate', [ProgrammeController::class, 'validatePublication']);
    Route::post('/programmes/{programme}/reject', [ProgrammeController::class, 'reject']);

    // Partenaires
    Route::apiResource('partenaires', PartenaireController::class);
    Route::post('/partenaires/{partenaire}/submit', [PartenaireController::class, 'submit']);
    Route::post('/partenaires/{partenaire}/validate', [PartenaireController::class, 'validatePublication']);
    Route::post('/partenaires/{partenaire}/reject', [PartenaireController::class, 'reject']);

    // Membres du bureau
    Route::apiResource('membres-bureau', MembreBureauController::class);
    Route::post('/membres-bureau/{membre_bureau}/submit', [MembreBureauController::class, 'submit']);
    Route::post('/membres-bureau/{membre_bureau}/validate', [MembreBureauController::class, 'validatePublication']);
    Route::post('/membres-bureau/{membre_bureau}/reject', [MembreBureauController::class, 'reject']);

    // Albums
    Route::apiResource('albums', AlbumController::class);
    Route::post('/albums/{album}/submit', [AlbumController::class, 'submit']);
    Route::post('/albums/{album}/validate', [AlbumController::class, 'validatePublication']);
    Route::post('/albums/{album}/reject', [AlbumController::class, 'reject']);
    Route::post('/albums/{album}/photos', [AlbumController::class, 'addPhotos']);
    Route::delete('/photos/{photo}', [AlbumController::class, 'deletePhoto']);

    // Messages contact (consultation admin)
    Route::get('/messages-contact', [MessageContactController::class, 'index']);
    Route::get('/messages-contact/{messageContact}', [MessageContactController::class, 'show']);
});
