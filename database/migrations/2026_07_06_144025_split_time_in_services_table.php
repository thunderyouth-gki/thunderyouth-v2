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
        Schema::table('services', function (Blueprint $table) {
            if (Schema::hasColumn('services', 'time')) {
                $table->renameColumn('time', 'start_time');
            }
        });

        Schema::table('services', function (Blueprint $table) {
            if (! Schema::hasColumn('services', 'end_time')) {
                $table->string('end_time')->nullable()->after('start_time');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('end_time');
            $table->renameColumn('start_time', 'time');
        });
    }
};
