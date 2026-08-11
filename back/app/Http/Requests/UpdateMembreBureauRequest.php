<?php
// app/Http/Requests/UpdateMembreBureauRequest.php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMembreBureauRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom' => ['sometimes', 'required', 'string', 'max:255'],
            'poste' => ['sometimes', 'required', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'ordre_affichage' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
