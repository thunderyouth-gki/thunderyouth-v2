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
        Schema::rename('services', 'events');

        Schema::table('events', function (Blueprint $table) {
            $table->renameColumn('service_date', 'event_date');
            $table->foreignId('event_type_id')->nullable()->after('id')->constrained('event_types')->nullOnDelete();
            $table->string('custom_event_type')->nullable()->after('event_type_id');
            $table->string('service_type')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['event_type_id']);
            $table->dropColumn(['event_type_id', 'custom_event_type']);
            $table->renameColumn('event_date', 'service_date');
        });

        Schema::rename('events', 'services');
    }
};
