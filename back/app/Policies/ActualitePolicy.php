<?php
// app/Policies/ActualitePolicy.php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\StatutPublication;
use App\Models\Actualite;
use App\Models\User;

class ActualitePolicy
{
    public function create(User $user): bool
    {
        return $user->hasRole('president') || $user->hasRole('secretaire_general');
    }

    public function update(User $user, Actualite $actualite): bool
    {
        if ($user->hasRole('president')) {
            return true;
        }

        if ($user->hasRole('secretaire_general')) {
            return $actualite->auteur_id === $user->id
                && in_array($actualite->statut, [StatutPublication::Brouillon, StatutPublication::Rejete], true);
        }

        return false;
    }

    public function delete(User $user, Actualite $actualite): bool
    {
        return $user->hasRole('president');
    }

    public function submit(User $user, Actualite $actualite): bool
    {
        if (!$user->hasRole('secretaire_general')) {
            return false;
        }

        return $actualite->auteur_id === $user->id
            && in_array($actualite->statut, [StatutPublication::Brouillon, StatutPublication::Rejete], true);
    }

    public function validate(User $user, Actualite $actualite): bool
    {
        if (!$user->hasRole('president')) {
            return false;
        }

        return $actualite->statut === StatutPublication::EnAttente;
    }

    public function reject(User $user, Actualite $actualite): bool
    {
        if (!$user->hasRole('president')) {
            return false;
        }

        return $actualite->statut === StatutPublication::EnAttente;
    }
}
