<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $fillable = [
        'album_id',
        'chemin_image',
    ];

    public function album()
    {
        return $this->belongsTo(Album::class);
    }
}
