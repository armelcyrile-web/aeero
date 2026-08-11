<?php
// database/migrations/xxxx_xx_xx_000004_create_albums_table.php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('albums', function (Blueprint $table): void {
            $table->id();
            $table->string('titre');
            $table->foreignId('evenement_id')->nullable()->constrained('evenements')->cascadeOnDelete();
            $table->foreignId('programme_id')->nullable()->constrained('programmes')->cascadeOnDelete();
            $table->string('statut')->default('brouillon');
            $table->text('motif_rejet')->nullable();
            $table->foreignId('auteur_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('valide_par_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('albums');
    }
};
