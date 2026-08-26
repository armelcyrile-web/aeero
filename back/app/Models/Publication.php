<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Publication extends Model
{
    protected $fillable = [
        'titre',
        'contenu',
        'type',
        'image',
        'date_publication',
    ];

    public function scopeOrdered($query)
    {
        return $query->orderBy('date_publication', 'desc');
    }
}
