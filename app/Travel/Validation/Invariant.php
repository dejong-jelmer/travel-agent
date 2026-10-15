<?php

namespace App\Travel\Validation;

/**
 * A rule that every leg or stay must keep, however it is written. Form requests show the Dutch message on the field;
 * the model guard and the actions throw a domain exception naming the case.
 */
enum Invariant
{
    case TransferStaysInOnePlace;
    case OnlyTransitCarriesLines;
    case LinesMatchTheLeg;
    case ArrivalRouteHasNoClock;
    case TimeRunsForward;
    case TimesSetTheDuration;
    case DatesRunForward;

    /**
     * The form field the message belongs to.
     */
    public function field(): string
    {
        return match ($this) {
            self::TransferStaysInOnePlace => 'to_station_id',
            self::OnlyTransitCarriesLines, self::LinesMatchTheLeg => 'transit_lines',
            self::ArrivalRouteHasNoClock => 'departure_at',
            self::TimeRunsForward => 'arrival_at',
            self::TimesSetTheDuration => 'duration_minutes',
            self::DatesRunForward => 'departure',
        };
    }

    public function message(): string
    {
        return match ($this) {
            self::TransferStaysInOnePlace => 'Een overstap begint en eindigt op hetzelfde station.',
            self::OnlyTransitCarriesLines => 'Een overstap, wandeling of taxirit heeft geen lijnen.',
            self::LinesMatchTheLeg => 'Elke lijn moet hetzelfde vervoermiddel hebben als de etappe.',
            self::ArrivalRouteHasNoClock => 'Een aankomstroute wordt voor elke boeking hergebruikt en heeft daarom geen vertrek- of aankomsttijd.',
            self::TimeRunsForward => 'De aankomsttijd mag niet voor de vertrektijd liggen.',
            self::TimesSetTheDuration => 'Laat de duur leeg als vertrek- en aankomsttijd zijn ingevuld: de duur volgt dan uit de tijden.',
            self::DatesRunForward => 'De vertrekdatum mag niet voor de aankomstdatum liggen.',
        };
    }
}
