<?php
// app/Http/Controllers/Api/MembreBureauPubliqueController.php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\StatutPublication;
use App\Http\Controllers\Controller;
use App\Models\MembreBureau;
use Illuminate\Http\JsonResponse;

class MembreBureauPubliqueController extends Controller
{
    public function index(): JsonResponse
    {
        $membres = MembreBureau::where('statut', StatutPublication::Publie)
            ->orderBy('ordre_affichage')
            ->get();

        return response()->json($membres);
    }
}
