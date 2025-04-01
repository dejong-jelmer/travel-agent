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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->string('name');
            $table->text('description');
            $table->decimal('price', 8, 2)->default(0.00)->nullable(false);
            $table->integer('duration');
            // $table->string('image');
            $table->boolean('active')->default(true);
            $table->boolean('featured')->default(false);
            $table->timestamp('published_at')->nullable();
            // $table->foreignId('itinerary_id')
            //     ->constrained()
            //     ->onDelete('cascade');
            // $table->foreignId('category_id')->constrained();
            $table->softDeletes();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
