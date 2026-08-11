<?php
// app/Policies/EvenementPolicy.php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\StatutPublication;
use App\Models\Evenement;
use App\Models\User;

class EvenementPolicy
{
    public function create(User $user): bool
    {
        return $user->hasRole('president') || $user->hasRole('secretaire_general');
    }

    public function update(User $user, Evenement $evenement): bool
    {
        if ($user->hasRole('president')) {
            return true;
        }

        if ($user->hasRole('secretaire_general')) {
            return $evenement->auteur_id === $user->id
                && in_array($evenement->statut, [StatutPublication::Brouillon, StatutPublication::Rejete], true);
        }

        return false;
    }

    public function delete(User $user, Evenement $evenement): bool
    {
        return $user->hasRole('president');
    }

    public function submit(User $user, Evenement $evenement): bool
    {
        if (!$user->hasRole('secretaire_general')) {
            return false;
        }

        return $evenement->auteur_id === $user->id
            && in_array($evenement->statut, [StatutPublication::Brouillon, StatutPublication::Rejete], true);
    }

    public function validate(User $user, Evenement $evenement): bool
    {
        if (!$user->hasRole('president')) {
            return false;
        }

        return $evenement->statut === StatutPublication::EnAttente;
    }

    public function reject(User $user, Evenement $evenement): bool
    {
        if (!$user->hasRole('president')) {
            return false;
        }

        return $evenement->statut === StatutPublication::EnAttente;
    }
}
