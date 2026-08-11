<?php
// app/Http/Controllers/Api/PartenairePubliqueController.php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\StatutPublication;
use App\Http\Controllers\Controller;
use App\Models\Partenaire;
use Illuminate\Http\JsonResponse;

class PartenairePubliqueController extends Controller
{
    public function index(): JsonResponse
    {
        $partenaires = Partenaire::where('statut', StatutPublication::Publie)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($partenaires);
    }
}
