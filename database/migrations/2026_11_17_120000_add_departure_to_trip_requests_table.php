<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trip_requests', function (Blueprint $table) {
            // The departure a request for a group trip is made on. A request keeps its other details when the
            // departure is deleted.
            $table->foreignId('departure_id')->nullable()->after('trip_id')->constrained()->nullOnDelete();
            $table->boolean('single_room')->default(false)->after('travelers_count');
            // The departure was full: the visitor wants to hear when a place comes free
            $table->boolean('interest_only')->default(false)->after('single_room');
        });
    }

    public function down(): void
    {
        Schema::table('trip_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('departure_id');
            $table->dropColumn(['single_room', 'interest_only']);
        });
    }
};
