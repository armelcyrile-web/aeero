<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AlbumController extends Controller
{
    public function index()
    {
        return Album::withCount('photos')->get();
    }

    public function show($id)
    {
        return Album::with('photos')->findOrFail($id);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'cover_image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('cover_image')) {
            $path = Storage::disk('cloudinary')->putFile('albums/covers', $request->file('cover_image'));
            $data['cover_image'] = Storage::disk('cloudinary')->url($path);
            $data['cover_image_path'] = $path;
        }

        return Album::create($data);
    }

    public function update(Request $request, $id)
    {
        $album = Album::findOrFail($id);

        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'cover_image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('cover_image')) {
            if ($album->cover_image_path) {
                Storage::disk('cloudinary')->delete($album->cover_image_path);
            }
            $path = Storage::disk('cloudinary')->putFile('albums/covers', $request->file('cover_image'));
            $data['cover_image'] = Storage::disk('cloudinary')->url($path);
            $data['cover_image_path'] = $path;
        }

        $album->update($data);

        return $album;
    }

    public function destroy($id)
    {
        $album = Album::with('photos')->findOrFail($id);

        if ($album->cover_image_path) {
            Storage::disk('cloudinary')->delete($album->cover_image_path);
        }

        foreach ($album->photos as $photo) {
            Storage::disk('cloudinary')->delete($photo->chemin_image);
            $photo->delete();
        }

        $album->delete();

        return response()->json(['message' => 'Album supprimé.']);
    }

    public function addPhotos(Request $request, $albumId)
    {
        $album = Album::findOrFail($albumId);

        $request->validate([
            'photos' => 'required|array',
            'photos.*' => 'image|max:2048',
        ]);

        $photos = [];

        foreach ($request->file('photos') as $file) {
            $path = Storage::disk('cloudinary')->putFile('albums', $file);
            $photos[] = $album->photos()->create([
                'chemin_image' => $path,
            ]);
        }

        return response()->json($photos, 201);
    }
}
