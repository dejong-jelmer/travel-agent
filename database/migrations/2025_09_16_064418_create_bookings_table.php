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
<<<<<<< HEAD
            $table->unsignedSmallInteger('total_adults');
            $table->unsignedSmallInteger('total_children');
=======
>>>>>>> b9e884b3fa401a1a668de43a24b0f92b9502b33e
            $table->unsignedInteger('price_per_person');
            $table->unsignedInteger('single_supplement');
            $table->unsignedInteger('base_total_price');
            $table->unsignedInteger('grand_total_price');
            $table->json('fees_and_funds');
<<<<<<< HEAD
            $table->text('internal_notes')->nullable();
            $table->timestamp('anonymized_at')->nullable();
=======
>>>>>>> b9e884b3fa401a1a668de43a24b0f92b9502b33e
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
