<?php
// app/Models/User.php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function actualitesCreees(): HasMany
    {
        return $this->hasMany(Actualite::class, 'auteur_id');
    }

    public function actualitesValidees(): HasMany
    {
        return $this->hasMany(Actualite::class, 'valide_par_id');
    }

    public function evenementsCrees(): HasMany
    {
        return $this->hasMany(Evenement::class, 'auteur_id');
    }

    public function evenementsValides(): HasMany
    {
        return $this->hasMany(Evenement::class, 'valide_par_id');
    }

    public function programmesCrees(): HasMany
    {
        return $this->hasMany(Programme::class, 'auteur_id');
    }

    public function programmesValides(): HasMany
    {
        return $this->hasMany(Programme::class, 'valide_par_id');
    }

    public function albumsCrees(): HasMany
    {
        return $this->hasMany(Album::class, 'auteur_id');
    }

    public function albumsValides(): HasMany
    {
        return $this->hasMany(Album::class, 'valide_par_id');
    }

    public function partenairesCrees(): HasMany
    {
        return $this->hasMany(Partenaire::class, 'auteur_id');
    }

    public function partenairesValides(): HasMany
    {
        return $this->hasMany(Partenaire::class, 'valide_par_id');
    }

    public function membresBureauCrees(): HasMany
    {
        return $this->hasMany(MembreBureau::class, 'auteur_id');
    }

    public function membresBureauValides(): HasMany
    {
        return $this->hasMany(MembreBureau::class, 'valide_par_id');
    }
}
