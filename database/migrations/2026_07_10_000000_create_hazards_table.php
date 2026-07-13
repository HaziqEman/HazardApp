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
        Schema::create('hazards', function (Blueprint $table) {
            $table->id();
            $table->string('user_name');
            $table->enum('hazard_category', ['Road Hazard', 'Environmental Hazard', 'Building Hazard']);
            $table->text('hazard_description');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('location_name')->nullable();
            $table->string('device_info')->nullable();
            $table->dateTime('reported_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hazards');
    }
};
