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
        Schema::create('travel_pharmacies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')
                ->constrained('travel_cities')
                ->restrictOnDelete();

            $table->string('name');
            $table->string('address')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_pharmacies');
    }
};
