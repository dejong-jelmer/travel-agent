<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedSmallInteger('margin_basis_points')->nullable()->after('grand_total_price');
            $table->unsignedInteger('calculated_price')->nullable()->after('margin_basis_points');
            $table->unsignedInteger('final_price')->nullable()->after('calculated_price');

            $table->unsignedInteger('price_per_person')->nullable()->change();
            $table->unsignedInteger('single_supplement')->nullable()->change();
            $table->unsignedInteger('base_total_price')->nullable()->change();
            $table->unsignedInteger('grand_total_price')->nullable()->change();
            $table->json('fees_and_funds')->nullable()->change();
            $table->foreignId('trip_price_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['margin_basis_points', 'calculated_price', 'final_price']);
        });
    }
};
