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
        Schema::create('abonnements', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string("client_number");
            $table->string("user_number");
            $table->softDeletes();
            $table->foreignIdFor(App\Models\User::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(App\Models\Concours::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('status')->default('CREATED');
            $table->dateTime('activation_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abonnements');
    }
};
