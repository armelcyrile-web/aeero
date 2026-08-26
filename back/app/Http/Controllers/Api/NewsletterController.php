<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NewsletterController extends Controller
{
    private const BREVO_LIST_IDS = [1];

    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|unique:newsletter_subscribers,email',
        ]);

        $subscriber = NewsletterSubscriber::create($data);

        $response = Http::withHeaders([
            'api-key' => env('BREVO_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.brevo.com/v3/contacts', [
            'email' => $subscriber->email,
            'listIds' => self::BREVO_LIST_IDS,
        ]);

        if (!$response->successful()) {
            return response()->json([
                'message' => 'Inscription enregistrée localement, mais l’ajout à Brevo a échoué.',
            ], 502);
        }

        return response()->json([
            'message' => 'Inscription à la newsletter réussie.',
            'subscriber' => $subscriber,
        ], 201);
    }
}
