<?php

namespace App\Travel\Validation;

use App\Travel\Enums\LegType;
use App\Travel\Enums\TransitMode;
use App\Travel\Models\Accommodation;
use App\Travel\Models\Leg;
use App\Travel\Models\TransitLine;

class LegInvariants
{
    /**
     * The invariants a leg breaks on its own. Works on an unsaved leg too, so a form request can check the leg as it
     * would be saved.
     *
     * @return list<Invariant>
     */
    public static function check(Leg $leg): array
    {
        $broken = [];
        $hasTimes = $leg->departure_at !== null && $leg->arrival_at !== null;

        if ($leg->type === LegType::Transfer
            && ($leg->from_station_id === null || $leg->from_station_id !== $leg->to_station_id)) {
            $broken[] = Invariant::TransferStaysInOnePlace;
        }

        // An arrival route is reused for every booking, so a time would be wrong for the next one.
        if ($leg->legable_type === (new Accommodation)->getMorphClass()
            && ($leg->departure_at !== null || $leg->arrival_at !== null)) {
            $broken[] = Invariant::ArrivalRouteHasNoClock;
        }

        if ($hasTimes && $leg->arrival_at->lt($leg->departure_at)) {
            $broken[] = Invariant::TimeRunsForward;
        }

        // With both times set, they are the single source of the duration.
        if ($hasTimes && $leg->duration_minutes !== null) {
            $broken[] = Invariant::TimesSetTheDuration;
        }

        return $broken;
    }

    /**
     * The invariants broken by giving a leg of this type these transit lines. A leg type without a transit mode
     * (transfer, walk, taxi) carries no lines; any other leg only lines of its own mode.
     *
     * The model guard cannot check this, as attach() and sync() fire no model events, so the action that writes
     * the lines does.
     *
     * @param  iterable<TransitLine>  $lines
     * @return list<Invariant>
     */
    public static function checkTransitLines(LegType $type, iterable $lines): array
    {
        $mode = TransitMode::tryFrom($type->value);

        foreach ($lines as $line) {
            if ($mode === null) {
                return [Invariant::OnlyTransitCarriesLines];
            }

            if ($line->mode !== $mode) {
                return [Invariant::LinesMatchTheLeg];
            }
        }

        return [];
    }
}
