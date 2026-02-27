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
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->string('name');
            $table->text('description');
            $table->decimal('price', 8, 2)->default(0.00)->nullable(false);
            $table->integer('duration');
            $table->json('transport')->nullable();
            $table->boolean('featured')->default(true);
            $table->dateTime('published_at');
            $table->json('highlights')->nullable();
            $table->json('practical_info')->nullable();
            $table->json('blocked_dates')->nullable();
            $table->string('meta_title', 60)->nullable();
            $table->text('meta_description', 160)->nullable();
            $table->softDeletes();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
