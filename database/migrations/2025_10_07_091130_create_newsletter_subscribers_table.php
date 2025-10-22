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
        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('name')->nullable();
            $table->string('token')->unique()->nullable();
            $table->string('confirmation_token')->unique()->nullable();
            $table->dateTime('confirmed_at')->nullable();
            $table->dateTime('confirmation_expires_at')->nullable();
            $table->string('unsubscribe_token')->unique()->nullable();
            $table->dateTime('subscribed_at');
            $table->dateTime('unsubscribed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscribers');
    }
};
