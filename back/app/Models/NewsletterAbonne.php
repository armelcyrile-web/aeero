<?php
// app/Models/NewsletterAbonne.php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterAbonne extends Model
{
    protected $table = 'newsletter_abonnes';

    protected $fillable = [
        'email',
    ];

    protected function casts(): array
    {
        return [];
    }
}
