<?php
// app/Enums/StatutPublication.php

declare(strict_types=1);

namespace App\Enums;

enum StatutPublication: string
{
    case Brouillon = 'brouillon';
    case EnAttente = 'en_attente';
    case Publie = 'publie';
    case Rejete = 'rejete';
}
