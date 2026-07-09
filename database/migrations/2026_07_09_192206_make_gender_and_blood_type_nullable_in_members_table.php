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
        Schema::table('members', function (Blueprint $table) {
            $table->enum('gender', ['L', 'P'])->nullable()->default(null)->change();
            $table->enum('blood_type', ['A', 'B', 'AB', 'O'])->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->enum('gender', ['L', 'P'])->default('L')->nullable(false)->change();
            $table->enum('blood_type', ['A', 'B', 'AB', 'O'])->default('O')->nullable(false)->change();
        });
    }
};
