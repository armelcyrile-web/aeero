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
            $data['photo'] = $request->file('photo')->store('bureau', 'public');
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
            if ($membre->photo) {
                Storage::disk('public')->delete($membre->photo);
            }
            $data['photo'] = $request->file('photo')->store('bureau', 'public');
        }

        $membre->update($data);

        return $membre;
    }

    public function destroy($id)
    {
        $membre = MembreBureau::findOrFail($id);

        if ($membre->photo) {
            Storage::disk('public')->delete($membre->photo);
        }

        $membre->delete();

        return response()->json(['message' => 'Membre supprimé.']);
    }
}
