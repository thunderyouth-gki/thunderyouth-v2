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
        Schema::create('duty_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Grup 1", "Grup 2"
            $table->json('composition')->nullable(); // JSON containing the template for WL1, Pianis, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('duty_groups');
    }
};
