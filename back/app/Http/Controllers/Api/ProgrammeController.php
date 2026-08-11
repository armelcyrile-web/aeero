<?php
// app/Http/Controllers/Api/ProgrammeController.php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\StatutPublication;
use App\Http\Controllers\Controller;
use App\Http\Requests\RejectProgrammeRequest;
use App\Http\Requests\StoreProgrammeRequest;
use App\Http\Requests\UpdateProgrammeRequest;
use App\Models\Programme;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProgrammeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Programme::query()->with(['auteur', 'validateur']);

        if ($request->has('statut')) {
            $query->where('statut', $request->input('statut'));
        }

        $programmes = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($programmes);
    }

    public function store(StoreProgrammeRequest $request): JsonResponse
    {
        $this->authorize('create', Programme::class);

        $data = $request->validated();
        $data['auteur_id'] = auth()->id();
        $data['statut'] = StatutPublication::Brouillon;
        $data['slug'] = Str::slug($data['titre']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('programmes', 'public');
        }

        $programme = Programme::create($data);

        return response()->json($programme, 201);
    }

    public function show(Programme $programme): JsonResponse
    {
        $programme->load(['auteur', 'validateur', 'albums']);

        return response()->json($programme);
    }

    public function update(UpdateProgrammeRequest $request, Programme $programme): JsonResponse
    {
        $this->authorize('update', $programme);

        $data = $request->validated();

        if (isset($data['titre'])) {
            $data['slug'] = Str::slug($data['titre']);
        }

        if ($request->hasFile('image')) {
            if ($programme->image) {
                Storage::disk('public')->delete($programme->image);
            }
            $data['image'] = $request->file('image')->store('programmes', 'public');
        }

        $programme->update($data);

        return response()->json($programme);
    }

    public function destroy(Programme $programme): JsonResponse
    {
        $this->authorize('delete', $programme);

        if ($programme->image) {
            Storage::disk('public')->delete($programme->image);
        }

        $programme->delete();

        return response()->json(null, 204);
    }

    public function submit(Programme $programme): JsonResponse
    {
        $this->authorize('submit', $programme);

        $programme->update(['statut' => StatutPublication::EnAttente]);

        // TODO: Envoyer une notification/email au président

        return response()->json($programme);
    }

    public function validatePublication(Programme $programme): JsonResponse
    {
        $this->authorize('validate', $programme);

        $programme->update([
            'statut' => StatutPublication::Publie,
            'published_at' => now(),
            'valide_par_id' => auth()->id(),
        ]);

        // TODO: Déclencher l'envoi de la newsletter aux abonnés

        return response()->json($programme);
    }

    public function reject(RejectProgrammeRequest $request, Programme $programme): JsonResponse
    {
        $this->authorize('reject', $programme);

        $programme->update([
            'statut' => StatutPublication::Rejete,
            'motif_rejet' => $request->input('motif_rejet'),
        ]);

        return response()->json($programme);
    }
}
