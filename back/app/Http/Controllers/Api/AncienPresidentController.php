<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AncienPresident;
use Illuminate\Http\Request;

class AncienPresidentController extends Controller
{
    public function index()
    {
        return AncienPresident::ordered()->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'periode_debut' => 'required|date_format:Y',
            'periode_fin' => 'nullable|date_format:Y',
        ]);

        return AncienPresident::create($data);
    }

    public function update(Request $request, $id)
    {
        $ancienPresident = AncienPresident::findOrFail($id);

        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'periode_debut' => 'required|date_format:Y',
            'periode_fin' => 'nullable|date_format:Y',
        ]);

        $ancienPresident->update($data);

        return $ancienPresident;
    }

    public function destroy($id)
    {
        $ancienPresident = AncienPresident::findOrFail($id);

        $ancienPresident->delete();

        return response()->json(['message' => 'Ancien président supprimé.']);
    }
}
