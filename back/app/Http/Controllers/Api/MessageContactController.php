<?php
// app/Http/Controllers/Api/MessageContactController.php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMessageContactRequest;
use App\Models\MessageContact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageContactController extends Controller
{
    public function store(StoreMessageContactRequest $request): JsonResponse
    {
        $message = MessageContact::create($request->validated());

        // TODO: Envoyer un email à l'association avec le contenu du message

        return response()->json(['message' => 'Message envoyé avec succès'], 201);
    }

    public function index(Request $request): JsonResponse
    {
        $query = MessageContact::query()->orderBy('created_at', 'desc');

        if ($request->has('lu')) {
            $query->where('lu', $request->boolean('lu'));
        }

        $messages = $query->paginate(20);

        return response()->json($messages);
    }

    public function show(MessageContact $messageContact): JsonResponse
    {
        if (!$messageContact->lu) {
            $messageContact->update(['lu' => true]);
        }

        return response()->json($messageContact);
    }
}
