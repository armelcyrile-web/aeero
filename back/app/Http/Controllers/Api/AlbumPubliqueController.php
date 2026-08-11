<?php
// app/Http/Controllers/Api/AlbumPubliqueController.php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\StatutPublication;
use App\Http\Controllers\Controller;
use App\Models\Album;
use Illuminate\Http\JsonResponse;

class AlbumPubliqueController extends Controller
{
    public function index(): JsonResponse
    {
        $albums = Album::where('statut', StatutPublication::Publie)
            ->with(['photos', 'evenement:id,titre', 'programme:id,titre'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($albums);
    }
}
