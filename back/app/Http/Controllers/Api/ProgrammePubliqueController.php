<?php
// app/Http/Controllers/Api/ProgrammePubliqueController.php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\StatutPublication;
use App\Http\Controllers\Controller;
use App\Models\Programme;
use Illuminate\Http\JsonResponse;

class ProgrammePubliqueController extends Controller
{
    public function index(): JsonResponse
    {
        $programmes = Programme::where('statut', StatutPublication::Publie)
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return response()->json($programmes);
    }

    public function show(string $slug): JsonResponse
    {
        $programme = Programme::where('slug', $slug)
            ->where('statut', StatutPublication::Publie)
            ->firstOrFail();

        return response()->json($programme);
    }
}
