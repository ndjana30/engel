<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rules', function (Blueprint $table) {
            $table->id();
            $table->string('libelle');
            $table->string('description')->nullable(true);
            $table->timestamps();
        });
        DB::table('rules')->insert(
            [
                [
                    'libelle' => 'Admin',
                    'description' => 'Administrateur avec tous les droits',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'libelle' => 'User',
                    'description' => 'Utilisateur standard',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rules');
    }
};
