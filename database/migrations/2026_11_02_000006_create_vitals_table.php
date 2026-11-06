<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Vital signs recorded by nurses (FR5).
        Schema::create('vitals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('temperature', 4, 1)->nullable();   // °C
            $table->string('blood_pressure', 10)->nullable();   // e.g. 120/80
            $table->unsignedSmallInteger('pulse')->nullable();  // beats per minute
            $table->decimal('weight', 5, 1)->nullable();        // kg
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vitals');
    }
};
