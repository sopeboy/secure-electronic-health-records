<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Links doctors and nurses to the patients they care for.
        Schema::create('patient_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['patient_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_assignments');
    }
};
