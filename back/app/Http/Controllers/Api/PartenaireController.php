<?php
// app/Http/Controllers/Api/PartenaireController.php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\StatutPublication;
use App\Http\Controllers\Controller;
use App\Http\Requests\RejectPartenaireRequest;
use App\Http\Requests\StorePartenaireRequest;
use App\Http\Requests\UpdatePartenaireRequest;
use App\Models\Partenaire;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PartenaireController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Partenaire::query()->with(['auteur', 'validateur']);

        if ($request->has('statut')) {
            $query->where('statut', $request->input('statut'));
        }

        $partenaires = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($partenaires);
    }

    public function store(StorePartenaireRequest $request): JsonResponse
    {
        $this->authorize('create', Partenaire::class);

        $data = $request->validated();
        $data['auteur_id'] = auth()->id();
        $data['statut'] = StatutPublication::Brouillon;
        $data['logo'] = $request->file('logo')->store('partenaires', 'public');

        $partenaire = Partenaire::create($data);

        return response()->json($partenaire, 201);
    }

    public function show(Partenaire $partenaire): JsonResponse
    {
        $partenaire->load(['auteur', 'validateur']);

        return response()->json($partenaire);
    }

    public function update(UpdatePartenaireRequest $request, Partenaire $partenaire): JsonResponse
    {
        $this->authorize('update', $partenaire);

        $data = $request->validated();

        if ($request->hasFile('logo')) {
            Storage::disk('public')->delete($partenaire->logo);
            $data['logo'] = $request->file('logo')->store('partenaires', 'public');
        }

        $partenaire->update($data);

        return response()->json($partenaire);
    }

    public function destroy(Partenaire $partenaire): JsonResponse
    {
        $this->authorize('delete', $partenaire);

        Storage::disk('public')->delete($partenaire->logo);
        $partenaire->delete();

        return response()->json(null, 204);
    }

    public function submit(Partenaire $partenaire): JsonResponse
    {
        $this->authorize('submit', $partenaire);

        $partenaire->update(['statut' => StatutPublication::EnAttente]);

        // TODO: Envoyer une notification/email au président

        return response()->json($partenaire);
    }

    public function validatePublication(Partenaire $partenaire): JsonResponse
    {
        $this->authorize('validate', $partenaire);

        $partenaire->update([
            'statut' => StatutPublication::Publie,
            'published_at' => now(),
            'valide_par_id' => auth()->id(),
        ]);

        // TODO: Déclencher l'envoi de la newsletter aux abonnés

        return response()->json($partenaire);
    }

    public function reject(RejectPartenaireRequest $request, Partenaire $partenaire): JsonResponse
    {
        $this->authorize('reject', $partenaire);

        $partenaire->update([
            'statut' => StatutPublication::Rejete,
            'motif_rejet' => $request->input('motif_rejet'),
        ]);

        return response()->json($partenaire);
    }
}
