<?php
// app/Http/Requests/UpdateAlbumRequest.php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAlbumRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titre' => ['sometimes', 'required', 'string', 'max:255'],
            'evenement_id' => ['nullable', 'exists:evenements,id'],
            'programme_id' => ['nullable', 'exists:programmes,id'],
        ];
    }
}
