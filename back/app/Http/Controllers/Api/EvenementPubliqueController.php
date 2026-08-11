<?php
// app/Http/Controllers/Api/EvenementPubliqueController.php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\StatutPublication;
use App\Http\Controllers\Controller;
use App\Models\Evenement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EvenementPubliqueController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $evenements = Evenement::where('statut', StatutPublication::Publie)
            ->orderBy('date_debut', 'desc')
            ->paginate(12);

        return response()->json($evenements);
    }

    public function show(string $slug): JsonResponse
    {
        $evenement = Evenement::where('slug', $slug)
            ->where('statut', StatutPublication::Publie)
            ->firstOrFail();

        return response()->json($evenement);
    }
}
