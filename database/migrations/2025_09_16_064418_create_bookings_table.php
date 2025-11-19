<?php

use App\Enums\Booking\PaymentStatus;
use App\Enums\Booking\Status;
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
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('main_booker_id')->cascadeOnDelete()->nullable();
            $table->date('departure_date');
            $table->boolean('conditions_accepted')->default(false);
            $table->boolean('is_confirmed')->default(false);
            $table->string('status', 50)->default(Status::New);
            $table->string('payment_status', 50)->default(PaymentStatus::Pending);
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
