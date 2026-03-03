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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('reference')->unique()->nullable();
            $table->foreignId('trip_id')->constrained('trips')->cascadeOnDelete();
            $table->foreignId('trip_price_id');
            $table->foreignId('main_booker_id')->cascadeOnDelete()->nullable();
            $table->date('departure_date');
            $table->boolean('has_accepted_conditions')->default(false);
            $table->boolean('has_confirmed')->default(false);
            $table->string('status', 20)->index();
            $table->string('payment_status', 20)->index();
            $table->unsignedInteger('price_per_person');
            $table->unsignedInteger('single_supplement');
            $table->unsignedInteger('total_price');
            $table->json('fees_and_funds');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
