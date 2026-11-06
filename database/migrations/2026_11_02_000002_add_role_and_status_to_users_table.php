<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Nullable on purpose: a user with no role gets no access (default deny).
            $table->foreignId('role_id')->nullable()->after('password')
                  ->constrained('roles')->nullOnDelete();
            $table->string('status')->default('active')->after('role_id'); // active | suspended
            $table->timestamp('last_login_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
            $table->dropColumn(['status', 'last_login_at']);
        });
    }
};
