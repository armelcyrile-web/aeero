<?php
// app/Http/Controllers/Api/NewsletterAbonneController.php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNewsletterAbonneRequest;
use App\Models\NewsletterAbonne;
use Illuminate\Http\JsonResponse;

class NewsletterAbonneController extends Controller
{
    public function store(StoreNewsletterAbonneRequest $request): JsonResponse
    {
        $abonne = NewsletterAbonne::create($request->validated());

        return response()->json(['message' => 'Inscription réussie'], 201);
    }
}
