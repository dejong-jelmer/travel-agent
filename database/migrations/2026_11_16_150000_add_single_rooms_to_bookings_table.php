<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // The number of single rooms a booking on a departure pays the single supplement for
            $table->unsignedSmallInteger('single_rooms')->default(0)->after('single_supplement');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('single_rooms');
        });
    }
};
