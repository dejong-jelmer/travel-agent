<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('images', function (Blueprint $table) {
            $table->unsignedSmallInteger('order')->nullable()->after('is_primary');
        });

        // Backfill: number existing images per owner in creation order.
        DB::table('images')
            ->orderBy('id')
            ->get(['id', 'imageable_type', 'imageable_id'])
            ->groupBy(fn ($image) => $image->imageable_type.'#'.$image->imageable_id)
            ->each(function ($images) {
                $images->values()->each(function ($image, $order) {
                    DB::table('images')->where('id', $image->id)->update(['order' => $order]);
                });
            });
    }

    public function down(): void
    {
        Schema::table('images', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};
