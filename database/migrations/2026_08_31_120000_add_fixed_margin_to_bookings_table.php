<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->boolean('margin_in_percentage')->default(true)->after('margin_basis_points');
            $table->unsignedInteger('margin_amount')->nullable()->after('margin_in_percentage');
            $table->unsignedInteger('fee_per_person')->nullable()->after('margin_amount');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['margin_in_percentage', 'margin_amount', 'fee_per_person']);
        });
    }
};
