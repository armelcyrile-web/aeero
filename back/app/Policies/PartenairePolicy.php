<?php
// app/Policies/PartenairePolicy.php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\StatutPublication;
use App\Models\Partenaire;
use App\Models\User;

class PartenairePolicy
{
    public function create(User $user): bool
    {
        return $user->hasRole('president') || $user->hasRole('secretaire_general');
    }

    public function update(User $user, Partenaire $partenaire): bool
    {
        if ($user->hasRole('president')) {
            return true;
        }

        if ($user->hasRole('secretaire_general')) {
            return $partenaire->auteur_id === $user->id
                && in_array($partenaire->statut, [StatutPublication::Brouillon, StatutPublication::Rejete], true);
        }

        return false;
    }

    public function delete(User $user, Partenaire $partenaire): bool
    {
        return $user->hasRole('president');
    }

    public function submit(User $user, Partenaire $partenaire): bool
    {
        if (!$user->hasRole('secretaire_general')) {
            return false;
        }

        return $partenaire->auteur_id === $user->id
            && in_array($partenaire->statut, [StatutPublication::Brouillon, StatutPublication::Rejete], true);
    }

    public function validate(User $user, Partenaire $partenaire): bool
    {
        if (!$user->hasRole('president')) {
            return false;
        }

        return $partenaire->statut === StatutPublication::EnAttente;
    }

    public function reject(User $user, Partenaire $partenaire): bool
    {
        if (!$user->hasRole('president')) {
            return false;
        }

        return $partenaire->statut === StatutPublication::EnAttente;
    }
}
