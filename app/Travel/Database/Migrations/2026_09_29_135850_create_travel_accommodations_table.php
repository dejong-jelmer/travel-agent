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
        Schema::create('travel_accommodations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')
                ->constrained('travel_cities')
                ->restrictOnDelete();

            $table->string('name');
            $table->string('type');

            $table->string('address')->nullable();
            $table->string('phone')->nullable();

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();

            $table->string('map_image')->nullable();
            $table->text('walk_directions')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_accommodations');
    }
};
