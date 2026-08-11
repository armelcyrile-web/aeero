<?php
// app/Models/Photo.php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Photo extends Model
{
    protected $fillable = [
        'album_id',
        'chemin',
        'legende',
    ];

    protected function casts(): array
    {
        return [];
    }

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }
}
