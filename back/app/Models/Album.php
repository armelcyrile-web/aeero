<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    protected $fillable = [
        'titre',
        'description',
        'cover_image',
        'date',
    ];

    public function photos()
    {
        return $this->hasMany(Photo::class);
    }
}
