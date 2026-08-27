<?php

use App\Models\Trip;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Convert the legacy "Title: description" strings to ['title' => ..., 'description' => ...].
     */
    public function up(): void
    {
        Trip::withTrashed()->eachById(function (Trip $trip): void {
            if (blank($trip->highlights)) {
                return;
            }

            $highlights = collect($trip->highlights)->map(function (array|string $highlight): array {
                if (is_array($highlight)) {
                    return $highlight;
                }

                // Legacy format: "Lascaux IV: een verbluffende reconstructie van ...".
                // Best effort and lossy: a title that legitimately contains a colon
                // is split at that colon, and the exact spacing is not restorable.
                [$title, $description] = array_pad(explode(':', $highlight, 2), 2, null);

                return [
                    'title' => trim($title),
                    'description' => $description ? trim($description) : null,
                ];
            })->all();

            $trip->update(['highlights' => $highlights]);
        });
    }

    /**
     * Reverse the migration: back to a flat list of "Title: description" strings.
     *
     * Written straight to the database, since the model setter only accepts the new format.
     */
    public function down(): void
    {
        Trip::withTrashed()->eachById(function (Trip $trip): void {
            if (blank($trip->highlights)) {
                return;
            }

            $highlights = collect($trip->highlights)->map(function (array|string $highlight): string {
                if (is_string($highlight)) {
                    return $highlight;
                }

                return filled($highlight['description'] ?? null)
                    ? "{$highlight['title']}: {$highlight['description']}"
                    : $highlight['title'];
            })->all();

            DB::table('trips')
                ->where('id', $trip->id)
                ->update(['highlights' => json_encode($highlights)]);
        });
    }
};
