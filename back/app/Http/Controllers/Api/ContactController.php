<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ContactController extends Controller
{
    private const ASSOCIATION_EMAIL = 'aeero.ouoghi@gmail.com';

    public function send(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        $response = Http::withHeaders([
            'api-key' => env('BREVO_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', [
            'sender' => [
                'email' => self::ASSOCIATION_EMAIL,
                'name' => 'AEERO',
            ],
            'to' => [
                [
                    'email' => self::ASSOCIATION_EMAIL,
                    'name' => 'AEERO',
                ],
            ],
            'replyTo' => [
                'email' => $data['email'],
                'name' => $data['nom'],
            ],
            'subject' => 'Nouveau message de contact - Site AEERO',
            'htmlContent' => sprintf(
                '<p><strong>Nom :</strong> %s</p><p><strong>Email :</strong> %s</p><p><strong>Message :</strong></p><p>%s</p>',
                htmlspecialchars($data['nom']),
                htmlspecialchars($data['email']),
                nl2br(htmlspecialchars($data['message']))
            ),
        ]);

        if (!$response->successful()) {
            return response()->json([
                'message' => 'L’envoi de l’email a échoué. Veuillez réessayer plus tard.',
            ], 502);
        }

        return response()->json([
            'message' => 'Votre message a été envoyé avec succès.',
        ]);
    }
}
