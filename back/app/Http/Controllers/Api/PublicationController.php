<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicationController extends Controller
{
    public function index()
    {
        return Publication::ordered()->get();
    }

    public function show($id)
    {
        return Publication::findOrFail($id);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
            'type' => 'required|in:actualite,evenement,annonce',
            'date_publication' => 'required|date',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('publications', 'public');
        }

        return Publication::create($data);
    }

    public function update(Request $request, $id)
    {
        $publication = Publication::findOrFail($id);

        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
            'type' => 'required|in:actualite,evenement,annonce',
            'date_publication' => 'required|date',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($publication->image) {
                Storage::disk('public')->delete($publication->image);
            }
            $data['image'] = $request->file('image')->store('publications', 'public');
        }

        $publication->update($data);

        return $publication;
    }

    public function destroy($id)
    {
        $publication = Publication::findOrFail($id);

        if ($publication->image) {
            Storage::disk('public')->delete($publication->image);
        }

        $publication->delete();

        return response()->json(['message' => 'Publication supprimée.']);
    }
}
