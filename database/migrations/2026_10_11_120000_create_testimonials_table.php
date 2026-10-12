<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            // Null for a general testimonial, not tied to a trip
            $table->foreignId('trip_id')->nullable()->constrained('trips')->nullOnDelete();
            $table->text('quote');
            $table->text('body')->nullable();
            $table->string('author_name');
            $table->string('relationship', 20);
            $table->string('author_context', 120)->nullable();
            $table->date('travelled_on')->nullable();
            $table->string('link_url')->nullable();
            $table->string('link_label')->nullable();
            $table->boolean('is_published')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
