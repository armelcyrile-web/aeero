<?php
// app/Http/Controllers/Api/ActualitePubliqueController.php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\StatutPublication;
use App\Http\Controllers\Controller;
use App\Models\Actualite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActualitePubliqueController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $actualites = Actualite::where('statut', StatutPublication::Publie)
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return response()->json($actualites);
    }

    public function show(string $slug): JsonResponse
    {
        $actualite = Actualite::where('slug', $slug)
            ->where('statut', StatutPublication::Publie)
            ->firstOrFail();

        return response()->json($actualite);
    }
}
