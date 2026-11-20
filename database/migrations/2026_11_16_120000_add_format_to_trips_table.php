<?php

use App\Enums\Trip\TripFormat;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            // Every trip that exists is sold on a date the customer picks
            $table->string('format', 20)->default(TripFormat::Individual->value)->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn('format');
        });
    }
};
