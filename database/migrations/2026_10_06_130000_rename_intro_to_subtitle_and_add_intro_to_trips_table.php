<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Two separate Schema::table calls on purpose: Laravel runs added columns before a rename within one blueprint,
     * so the new `intro` would be added while the old `intro` still exists.
     */
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->renameColumn('intro', 'subtitle');
        });

        Schema::table('trips', function (Blueprint $table) {
            $table->text('intro')->nullable()->after('subtitle');
        });
    }

    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn('intro');
        });

        Schema::table('trips', function (Blueprint $table) {
            $table->renameColumn('subtitle', 'intro');
        });
    }
};
