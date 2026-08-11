<?php
// app/Policies/MembreBureauPolicy.php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\StatutPublication;
use App\Models\MembreBureau;
use App\Models\User;

class MembreBureauPolicy
{
    public function create(User $user): bool
    {
        return $user->hasRole('president') || $user->hasRole('secretaire_general');
    }

    public function update(User $user, MembreBureau $membreBureau): bool
    {
        if ($user->hasRole('president')) {
            return true;
        }

        if ($user->hasRole('secretaire_general')) {
            return $membreBureau->auteur_id === $user->id
                && in_array($membreBureau->statut, [StatutPublication::Brouillon, StatutPublication::Rejete], true);
        }

        return false;
    }

    public function delete(User $user, MembreBureau $membreBureau): bool
    {
        return $user->hasRole('president');
    }

    public function submit(User $user, MembreBureau $membreBureau): bool
    {
        if (!$user->hasRole('secretaire_general')) {
            return false;
        }

        return $membreBureau->auteur_id === $user->id
            && in_array($membreBureau->statut, [StatutPublication::Brouillon, StatutPublication::Rejete], true);
    }

    public function validate(User $user, MembreBureau $membreBureau): bool
    {
        if (!$user->hasRole('president')) {
            return false;
        }

        return $membreBureau->statut === StatutPublication::EnAttente;
    }

    public function reject(User $user, MembreBureau $membreBureau): bool
    {
        if (!$user->hasRole('president')) {
            return false;
        }

        return $membreBureau->statut === StatutPublication::EnAttente;
    }
}
