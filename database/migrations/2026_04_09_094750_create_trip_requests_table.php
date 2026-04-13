<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->nullable()->constrained('trips')->nullOnDelete();
            $table->string('name', 100);
            $table->string('email', 200);
            $table->string('phone', 30)->nullable();
            $table->string('preferred_month', 20)->nullable();
            $table->text('preferred_period_note')->nullable();
            $table->unsignedSmallInteger('travelers_count')->nullable();
            $table->string('departure_station', 100)->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('new')->index();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->boolean('consent_privacy');
            $table->timestamp('consent_privacy_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_requests');
    }
};
