<?php
// app/Http/Controllers/Api/AlbumController.php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\StatutPublication;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddPhotosRequest;
use App\Http\Requests\RejectAlbumRequest;
use App\Http\Requests\StoreAlbumRequest;
use App\Http\Requests\UpdateAlbumRequest;
use App\Models\Album;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Photo;
class AlbumController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Album::query()->with(['auteur', 'evenement', 'programme']);

        if ($request->has('statut')) {
            $query->where('statut', $request->input('statut'));
        }

        $albums = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($albums);
    }

    public function store(StoreAlbumRequest $request): JsonResponse
    {
        $this->authorize('create', Album::class);

        $data = $request->validated();
        $data['auteur_id'] = auth()->id();
        $data['statut'] = StatutPublication::Brouillon;

        $album = Album::create($data);

        return response()->json($album, 201);
    }

    public function show(Album $album): JsonResponse
    {
        $album->load(['auteur', 'evenement', 'programme', 'photos']);

        return response()->json($album);
    }

    public function update(UpdateAlbumRequest $request, Album $album): JsonResponse
    {
        $this->authorize('update', $album);

        $album->update($request->validated());

        return response()->json($album);
    }

    public function destroy(Album $album): JsonResponse
    {
        $this->authorize('delete', $album);

        foreach ($album->photos as $photo) {
            Storage::disk('public')->delete($photo->chemin);
        }

        $album->delete();

        return response()->json(null, 204);
    }

    public function submit(Album $album): JsonResponse
    {
        $this->authorize('submit', $album);

        $album->update(['statut' => StatutPublication::EnAttente]);

        // TODO: Envoyer une notification/email au président

        return response()->json($album);
    }

    public function validatePublication(Album $album): JsonResponse
    {
        $this->authorize('validate', $album);

        $album->update([
            'statut' => StatutPublication::Publie,
            'published_at' => now(),
            'valide_par_id' => auth()->id(),
        ]);

        return response()->json($album);
    }

    public function reject(RejectAlbumRequest $request, Album $album): JsonResponse
    {
        $this->authorize('reject', $album);

        $album->update([
            'statut' => StatutPublication::Rejete,
            'motif_rejet' => $request->input('motif_rejet'),
        ]);

        return response()->json($album);
    }

    public function addPhotos(AddPhotosRequest $request, Album $album): JsonResponse
    {
        $this->authorize('update', $album);

        $photos = [];

        foreach ($request->input('photos') as $index => $photoData) {
            $file = $request->file("photos.{$index}.image");
            $path = $file->store("albums/{$album->id}", 'public');

            $photos[] = $album->photos()->create([
                'chemin' => $path,
                'legende' => $photoData['legende'] ?? null,
            ]);
        }

        return response()->json($photos, 201);
    }

    public function deletePhoto(Album $album, Photo $photo): JsonResponse
    {
        $this->authorize('update', $album);

        Storage::disk('public')->delete($photo->chemin);
        $photo->delete();

        return response()->json(null, 204);
    }
}
