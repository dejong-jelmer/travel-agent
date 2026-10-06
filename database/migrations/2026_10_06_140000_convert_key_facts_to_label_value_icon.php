<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Key facts used to be plain strings, they become ['label' => ..., 'value' => ..., 'icon' => ...].
     * The old text moves to `value`, the label is left empty to be filled in through the admin and the icon is the
     * neutral default (the `info` case of KeyFactIcon, written out so this migration does not depend on app code).
     */
    public function up(): void
    {
        $this->convert(fn (mixed $fact) => is_array($fact) ? $fact : [
            'label' => '',
            'value' => (string) $fact,
            'icon' => 'info',
        ]);
    }

    /**
     * Lossy by design: only `value` survives the rollback, the label and icon of each key fact are dropped.
     */
    public function down(): void
    {
        $this->convert(fn (mixed $fact) => is_array($fact) ? (string) ($fact['value'] ?? '') : $fact);
    }

    private function convert(callable $mapFact): void
    {
        DB::table('trips')->whereNotNull('key_facts')->orderBy('id')->each(function (object $trip) use ($mapFact) {
            $facts = json_decode($trip->key_facts, true);

            if (! is_array($facts) || $facts === []) {
                return;
            }

            DB::table('trips')->where('id', $trip->id)->update([
                'key_facts' => json_encode(array_map($mapFact, $facts)),
            ]);
        });
    }
};
