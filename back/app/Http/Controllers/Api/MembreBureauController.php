<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MembreBureau;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MembreBureauController extends Controller
{
    public function index()
    {
        return MembreBureau::ordered()->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'poste' => 'required|string|max:255',
            'photo' => 'nullable|image|max:2048',
            'ordre_affichage' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('photo')) {
            $path = Storage::disk('cloudinary')->putFile('bureau', $request->file('photo'));
            $data['photo'] = Storage::disk('cloudinary')->url($path);
            $data['photo_path'] = $path;
        }

        return MembreBureau::create($data);
    }

    public function update(Request $request, $id)
    {
        $membre = MembreBureau::findOrFail($id);

        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'poste' => 'required|string|max:255',
            'photo' => 'nullable|image|max:2048',
            'ordre_affichage' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('photo')) {
            if ($membre->photo_path) {
                Storage::disk('cloudinary')->delete($membre->photo_path);
            }
            $path = Storage::disk('cloudinary')->putFile('bureau', $request->file('photo'));
            $data['photo'] = Storage::disk('cloudinary')->url($path);
            $data['photo_path'] = $path;
        }

        $membre->update($data);

        return $membre;
    }

    public function destroy($id)
    {
        $membre = MembreBureau::findOrFail($id);

        if ($membre->photo_path) {
            Storage::disk('cloudinary')->delete($membre->photo_path);
        }

        $membre->delete();

        return response()->json(['message' => 'Membre supprimé.']);
    }
}
