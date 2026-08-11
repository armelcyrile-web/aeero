<?php
// app/Models/Programme.php

declare(strict_types=1);

namespace App\Models;

use App\Enums\StatutPublication;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Programme extends Model
{
    protected $fillable = [
        'titre',
        'slug',
        'description',
        'image',
        'statut',
        'motif_rejet',
        'auteur_id',
        'valide_par_id',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'statut' => StatutPublication::class,
        ];
    }

    public function auteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auteur_id');
    }

    public function validateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'valide_par_id');
    }

    public function albums(): HasMany
    {
        return $this->hasMany(Album::class, 'programme_id');
    }
}
