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
        Schema::create('travel_legs', function (Blueprint $table) {
            $table->id();
            $table->morphs('legable');

            $table->string('type');

            $table->foreignId('from_station_id')
                ->nullable()
                ->constrained('travel_stations')
                ->nullOnDelete();

            $table->foreignId('to_station_id')
                ->nullable()
                ->constrained('travel_stations')
                ->nullOnDelete();

            $table->dateTime('departure_at')->nullable();
            $table->dateTime('arrival_at')->nullable();

            $table->unsignedInteger('duration_minutes')->nullable();

            $table->string('number')->nullable();
            $table->string('carriage')->nullable();
            $table->string('seat')->nullable();
            $table->text('note')->nullable();

            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_legs');
    }
};
