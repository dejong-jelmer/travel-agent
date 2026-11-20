<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The trips a story goes with: shown below the story, and the story below each of those trips
        Schema::create('blog_post_trip', function (Blueprint $table) {
            $table->foreignId('blog_post_id')->constrained('blog_posts')->cascadeOnDelete();
            $table->foreignId('trip_id')->constrained('trips')->cascadeOnDelete();
            $table->primary(['blog_post_id', 'trip_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_post_trip');
    }
};
