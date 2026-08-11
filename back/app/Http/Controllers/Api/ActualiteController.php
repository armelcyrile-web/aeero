<?php
// app/Http/Controllers/Api/ActualiteController.php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\StatutPublication;
use App\Http\Controllers\Controller;
use App\Http\Requests\RejectActualiteRequest;
use App\Http\Requests\StoreActualiteRequest;
use App\Http\Requests\UpdateActualiteRequest;
use App\Models\Actualite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ActualiteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Actualite::query()->with(['auteur', 'validateur']);

        if ($request->has('statut')) {
            $query->where('statut', $request->input('statut'));
        }

        $actualites = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($actualites);
    }

   public function store(StoreActualiteRequest $request): JsonResponse
{
    $this->authorize('create', Actualite::class);

    $data = $request->validated();
    $data['auteur_id'] = auth()->id();
    $data['slug'] = Str::slug($data['titre']);

    if (auth()->user()->hasRole('president')) {
        $data['statut'] = StatutPublication::Publie;
        $data['published_at'] = now();
        $data['valide_par_id'] = auth()->id();
    } else {
        $data['statut'] = StatutPublication::Brouillon;
    }

    if ($request->hasFile('image')) {
        $data['image'] = $request->file('image')->store('actualites', 'public');
    }

    $actualite = Actualite::create($data);

    return response()->json($actualite, 201);
}

    public function show(Actualite $actualite): JsonResponse
    {
        $actualite->load(['auteur', 'validateur']);

        return response()->json($actualite);
    }

    public function update(UpdateActualiteRequest $request, Actualite $actualite): JsonResponse
    {
        $this->authorize('update', $actualite);

        $data = $request->validated();

        if (isset($data['titre'])) {
            $data['slug'] = Str::slug($data['titre']);
        }

        if ($request->hasFile('image')) {
            if ($actualite->image) {
                Storage::disk('public')->delete($actualite->image);
            }
            $data['image'] = $request->file('image')->store('actualites', 'public');
        }

        $actualite->update($data);

        return response()->json($actualite);
    }

    public function destroy(Actualite $actualite): JsonResponse
    {
        $this->authorize('delete', $actualite);

        if ($actualite->image) {
            Storage::disk('public')->delete($actualite->image);
        }

        $actualite->delete();

        return response()->json(null, 204);
    }

    public function submit(Actualite $actualite): JsonResponse
    {
        $this->authorize('submit', $actualite);

        $actualite->update(['statut' => StatutPublication::EnAttente]);

        // TODO: Envoyer une notification/email au président pour l'informer
        // d'une nouvelle actualité en attente de validation

        return response()->json($actualite);
    }

    public function validatePublication(Actualite $actualite): JsonResponse
    {
        $this->authorize('validate', $actualite);

        $actualite->update([
            'statut' => StatutPublication::Publie,
            'published_at' => now(),
            'valide_par_id' => auth()->id(),
        ]);

        // TODO: Déclencher l'envoi de la newsletter aux abonnés

        return response()->json($actualite);
    }

    public function reject(RejectActualiteRequest $request, Actualite $actualite): JsonResponse
    {
        $this->authorize('reject', $actualite);

        $actualite->update([
            'statut' => StatutPublication::Rejete,
            'motif_rejet' => $request->input('motif_rejet'),
        ]);

        return response()->json($actualite);
    }
}
