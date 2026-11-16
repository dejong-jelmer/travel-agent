<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained()->cascadeOnDelete();
            $table->date('departs_on');
            $table->date('returns_on');
            $table->unsignedSmallInteger('min_participants');
            $table->unsignedSmallInteger('max_participants');
            // In cents. Null falls back on the price of the trip for the day of departure.
            $table->unsignedInteger('price_per_person')->nullable();
            $table->unsignedInteger('single_supplement')->nullable();
            $table->date('decision_date');
            $table->timestamp('guaranteed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();

            $table->index(['trip_id', 'departs_on']);
            $table->index('decision_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departures');
    }
};
