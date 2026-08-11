<?php
// app/Policies/ProgrammePolicy.php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\StatutPublication;
use App\Models\Programme;
use App\Models\User;

class ProgrammePolicy
{
    public function create(User $user): bool
    {
        return $user->hasRole('president') || $user->hasRole('secretaire_general');
    }

    public function update(User $user, Programme $programme): bool
    {
        if ($user->hasRole('president')) {
            return true;
        }

        if ($user->hasRole('secretaire_general')) {
            return $programme->auteur_id === $user->id
                && in_array($programme->statut, [StatutPublication::Brouillon, StatutPublication::Rejete], true);
        }

        return false;
    }

    public function delete(User $user, Programme $programme): bool
    {
        return $user->hasRole('president');
    }

    public function submit(User $user, Programme $programme): bool
    {
        if (!$user->hasRole('secretaire_general')) {
            return false;
        }

        return $programme->auteur_id === $user->id
            && in_array($programme->statut, [StatutPublication::Brouillon, StatutPublication::Rejete], true);
    }

    public function validate(User $user, Programme $programme): bool
    {
        if (!$user->hasRole('president')) {
            return false;
        }

        return $programme->statut === StatutPublication::EnAttente;
    }

    public function reject(User $user, Programme $programme): bool
    {
        if (!$user->hasRole('president')) {
            return false;
        }

        return $programme->statut === StatutPublication::EnAttente;
    }
}
