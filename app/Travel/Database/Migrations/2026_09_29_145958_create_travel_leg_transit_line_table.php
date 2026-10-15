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
        Schema::create('travel_leg_transit_line', function (Blueprint $table) {
            $table->foreignId('leg_id')
                ->constrained('travel_legs')
                ->cascadeOnDelete();

            $table->foreignId('transit_line_id')
                ->constrained('travel_transit_lines')
                ->restrictOnDelete();

            $table->string('direction')->nullable();
            $table->unsignedInteger('position')->default(0);

            $table->primary(['leg_id', 'transit_line_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_leg_transit_line');
    }
};
