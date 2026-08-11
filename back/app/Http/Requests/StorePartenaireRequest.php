<?php
// app/Http/Requests/StorePartenaireRequest.php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePartenaireRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:255'],
            'logo' => ['required', 'image', 'max:2048'],
            'lien_site' => ['nullable', 'url', 'max:255'],
        ];
    }
}
