<?php
// app/Http/Controllers/Api/EvenementController.php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\StatutPublication;
use App\Http\Controllers\Controller;
use App\Http\Requests\RejectEvenementRequest;
use App\Http\Requests\StoreEvenementRequest;
use App\Http\Requests\UpdateEvenementRequest;
use App\Models\Evenement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EvenementController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Evenement::query()->with(['auteur', 'validateur']);

        if ($request->has('statut')) {
            $query->where('statut', $request->input('statut'));
        }

        $evenements = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($evenements);
    }

    public function store(StoreEvenementRequest $request): JsonResponse
    {
        $this->authorize('create', Evenement::class);

        $data = $request->validated();
        $data['auteur_id'] = auth()->id();
        $data['statut'] = StatutPublication::Brouillon;
        $data['slug'] = Str::slug($data['titre']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('evenements', 'public');
        }

        $evenement = Evenement::create($data);

        return response()->json($evenement, 201);
    }

    public function show(Evenement $evenement): JsonResponse
    {
        $evenement->load(['auteur', 'validateur', 'albums']);

        return response()->json($evenement);
    }

    public function update(UpdateEvenementRequest $request, Evenement $evenement): JsonResponse
    {
        $this->authorize('update', $evenement);

        $data = $request->validated();

        if (isset($data['titre'])) {
            $data['slug'] = Str::slug($data['titre']);
        }

        if ($request->hasFile('image')) {
            if ($evenement->image) {
                Storage::disk('public')->delete($evenement->image);
            }
            $data['image'] = $request->file('image')->store('evenements', 'public');
        }

        $evenement->update($data);

        return response()->json($evenement);
    }

    public function destroy(Evenement $evenement): JsonResponse
    {
        $this->authorize('delete', $evenement);

        if ($evenement->image) {
            Storage::disk('public')->delete($evenement->image);
        }

        $evenement->delete();

        return response()->json(null, 204);
    }

    public function submit(Evenement $evenement): JsonResponse
    {
        $this->authorize('submit', $evenement);

        $evenement->update(['statut' => StatutPublication::EnAttente]);

        // TODO: Envoyer une notification/email au président

        return response()->json($evenement);
    }

    public function validatePublication(Evenement $evenement): JsonResponse
    {
        $this->authorize('validate', $evenement);

        $evenement->update([
            'statut' => StatutPublication::Publie,
            'published_at' => now(),
            'valide_par_id' => auth()->id(),
        ]);

        // TODO: Déclencher l'envoi de la newsletter aux abonnés

        return response()->json($evenement);
    }

    public function reject(RejectEvenementRequest $request, Evenement $evenement): JsonResponse
    {
        $this->authorize('reject', $evenement);

        $evenement->update([
            'statut' => StatutPublication::Rejete,
            'motif_rejet' => $request->input('motif_rejet'),
        ]);

        return response()->json($evenement);
    }
}
