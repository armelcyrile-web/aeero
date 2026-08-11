<?php
// app/Http/Requests/AddPhotosRequest.php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddPhotosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'photos' => ['required', 'array', 'min:1'],
            'photos.*.image' => ['required', 'image', 'max:5120'],
            'photos.*.legende' => ['nullable', 'string', 'max:500'],
        ];
    }
}
