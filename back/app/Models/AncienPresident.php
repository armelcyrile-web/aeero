<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AncienPresident extends Model
{
    protected $fillable = [
        'nom',
        'prenom',
        'periode_debut',
        'periode_fin',
    ];

    public function scopeOrdered($query)
    {
        return $query->orderBy('periode_debut', 'desc');
    }
}
