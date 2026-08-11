<?php
// app/Policies/AlbumPolicy.php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\StatutPublication;
use App\Models\Album;
use App\Models\User;

class AlbumPolicy
{
    public function create(User $user): bool
    {
        return $user->hasRole('president') || $user->hasRole('secretaire_general');
    }

    public function update(User $user, Album $album): bool
    {
        if ($user->hasRole('president')) {
            return true;
        }

        if ($user->hasRole('secretaire_general')) {
            return $album->auteur_id === $user->id
                && in_array($album->statut, [StatutPublication::Brouillon, StatutPublication::Rejete], true);
        }

        return false;
    }

    public function delete(User $user, Album $album): bool
    {
        return $user->hasRole('president');
    }

    public function submit(User $user, Album $album): bool
    {
        if (!$user->hasRole('secretaire_general')) {
            return false;
        }

        return $album->auteur_id === $user->id
            && in_array($album->statut, [StatutPublication::Brouillon, StatutPublication::Rejete], true);
    }

    public function validate(User $user, Album $album): bool
    {
        if (!$user->hasRole('president')) {
            return false;
        }

        return $album->statut === StatutPublication::EnAttente;
    }

    public function reject(User $user, Album $album): bool
    {
        if (!$user->hasRole('president')) {
            return false;
        }

        return $album->statut === StatutPublication::EnAttente;
    }
}
