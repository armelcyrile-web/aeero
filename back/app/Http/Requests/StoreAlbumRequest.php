<?php
// app/Http/Requests/StoreAlbumRequest.php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAlbumRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titre' => ['required', 'string', 'max:255'],
            'evenement_id' => ['nullable', 'exists:evenements,id'],
            'programme_id' => ['nullable', 'exists:programmes,id'],
        ];
    }
}
