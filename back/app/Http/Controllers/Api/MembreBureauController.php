<?php
// app/Http/Controllers/Api/MembreBureauController.php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\StatutPublication;
use App\Http\Controllers\Controller;
use App\Http\Requests\RejectMembreBureauRequest;
use App\Http\Requests\StoreMembreBureauRequest;
use App\Http\Requests\UpdateMembreBureauRequest;
use App\Models\MembreBureau;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MembreBureauController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = MembreBureau::query()->with(['auteur', 'validateur']);

        if ($request->has('statut')) {
            $query->where('statut', $request->input('statut'));
        }

        $membres = $query->orderBy('ordre_affichage')->paginate(15);

        return response()->json($membres);
    }

    public function store(StoreMembreBureauRequest $request): JsonResponse
    {
        $this->authorize('create', MembreBureau::class);

        $data = $request->validated();
        $data['auteur_id'] = auth()->id();
        $data['statut'] = StatutPublication::Brouillon;

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('membres', 'public');
        }

        $membre = MembreBureau::create($data);

        return response()->json($membre, 201);
    }

    public function show(MembreBureau $membreBureau): JsonResponse
    {
        $membreBureau->load(['auteur', 'validateur']);

        return response()->json($membreBureau);
    }

    public function update(UpdateMembreBureauRequest $request, MembreBureau $membreBureau): JsonResponse
    {
        $this->authorize('update', $membreBureau);

        $data = $request->validated();

        if ($request->hasFile('photo')) {
            if ($membreBureau->photo) {
                Storage::disk('public')->delete($membreBureau->photo);
            }
            $data['photo'] = $request->file('photo')->store('membres', 'public');
        }

        $membreBureau->update($data);

        return response()->json($membreBureau);
    }

    public function destroy(MembreBureau $membreBureau): JsonResponse
    {
        $this->authorize('delete', $membreBureau);

        if ($membreBureau->photo) {
            Storage::disk('public')->delete($membreBureau->photo);
        }

        $membreBureau->delete();

        return response()->json(null, 204);
    }

    public function submit(MembreBureau $membreBureau): JsonResponse
    {
        $this->authorize('submit', $membreBureau);

        $membreBureau->update(['statut' => StatutPublication::EnAttente]);

        // TODO: Envoyer une notification/email au président

        return response()->json($membreBureau);
    }

    public function validatePublication(MembreBureau $membreBureau): JsonResponse
    {
        $this->authorize('validate', $membreBureau);

        $membreBureau->update([
            'statut' => StatutPublication::Publie,
            'published_at' => now(),
            'valide_par_id' => auth()->id(),
        ]);

        return response()->json($membreBureau);
    }

    public function reject(RejectMembreBureauRequest $request, MembreBureau $membreBureau): JsonResponse
    {
        $this->authorize('reject', $membreBureau);

        $membreBureau->update([
            'statut' => StatutPublication::Rejete,
            'motif_rejet' => $request->input('motif_rejet'),
        ]);

        return response()->json($membreBureau);
    }
}
