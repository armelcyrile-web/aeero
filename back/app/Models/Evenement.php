<?php
// app/Models/Evenement.php

declare(strict_types=1);

namespace App\Models;

use App\Enums\StatutPublication;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evenement extends Model
{
    protected $fillable = [
        'titre',
        'slug',
        'description',
        'date_debut',
        'date_fin',
        'lieu',
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
            'date_debut' => 'datetime',
            'date_fin' => 'datetime',
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
        return $this->hasMany(Album::class, 'evenement_id');
    }
}
