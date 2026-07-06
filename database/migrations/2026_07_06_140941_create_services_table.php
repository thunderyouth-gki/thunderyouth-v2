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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->date('service_date');
            $table->string('service_type'); // back_to_the_bible, sharing_sunday, kebaktian_gabungan, celebration_week, other
            $table->string('custom_service_type')->nullable();
            $table->string('theme')->nullable();
            $table->string('speaker')->nullable();
            $table->string('elder')->nullable();
            $table->string('bible_reading')->nullable();
            $table->string('time')->default('09.30 - selesai');
            $table->string('place')->default('Ruang Remaja Pemuda Lt. 1');
            $table->string('status')->default('draft'); // draft, published

            // JSON Columns for flexible data
            $table->json('liturgy_verses')->nullable();
            $table->json('liturgy_songs')->nullable();
            $table->json('duties')->nullable();

            // Post-service metrics
            $table->integer('attendance_male')->nullable();
            $table->integer('attendance_female')->nullable();
            $table->bigInteger('offering_amount')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
