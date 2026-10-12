<?php

namespace App\Travel\Documents;

use Carbon\CarbonInterface;

/**
 * How the documents write dates, times and durations: in Dutch, and as wall-clock times at the place itself.
 */
class Format
{
    /**
     * 'dinsdag 11 augustus'
     */
    public static function date(?CarbonInterface $date): string
    {
        return $date?->locale('nl')->isoFormat('dddd D MMMM') ?? '';
    }

    /**
     * 'di 11 aug'
     */
    public static function shortDate(?CarbonInterface $date): string
    {
        return $date ? str_replace('.', '', $date->locale('nl')->isoFormat('dd D MMM')) : '';
    }

    /**
     * '12:47'
     */
    public static function time(?CarbonInterface $moment): string
    {
        return $moment?->format('H:i') ?? '';
    }

    /**
     * '16:00', from a time column, which the database gives as '16:00:00'.
     */
    public static function timeOfDay(?string $time): string
    {
        return $time === null ? '' : substr($time, 0, 5);
    }

    /**
     * 'Inchecken vanaf 16:00, uitchecken voor 11:30', from the time columns, leaving out what is not known.
     */
    public static function checkTimes(?string $checkIn, ?string $checkOut): string
    {
        return ucfirst(implode(', ', array_filter([
            $checkIn === null ? null : 'inchecken vanaf '.self::timeOfDay($checkIn),
            $checkOut === null ? null : 'uitchecken voor '.self::timeOfDay($checkOut),
        ])));
    }

    /**
     * '54 min', '2 u', '2 u 16 min'
     */
    public static function duration(?int $minutes): string
    {
        if ($minutes === null) {
            return '';
        }

        $hours = intdiv($minutes, 60);
        $rest = $minutes % 60;

        return match (true) {
            $hours === 0 => "{$rest} min",
            $rest === 0 => "{$hours} u",
            default => "{$hours} u {$rest} min",
        };
    }

    /**
     * '1 nacht', '3 nachten'
     */
    public static function nights(?int $nights): string
    {
        return match ($nights) {
            null => '',
            1 => '1 nacht',
            default => "{$nights} nachten",
        };
    }

    /**
     * 'Nr. 8413 · Rijtuig 8 - onder · Stoel 824', leaving out what the ticket does not have.
     */
    public static function ticket(?string $number, ?string $carriage, ?string $seat): string
    {
        return implode(' · ', array_filter([
            $number === null ? null : "Nr. {$number}",
            $carriage === null ? null : "Rijtuig {$carriage}",
            $seat === null ? null : "Stoel {$seat}",
        ]));
    }

    /**
     * 'Nijmegen → Utrecht Centraal'. A transfer stays at one station; an arrival route can start without one.
     */
    public static function stations(?string $from, ?string $to): string
    {
        return match (true) {
            $from !== null && $to !== null => $from === $to ? $from : "{$from} → {$to}",
            $from !== null => $from,
            $to !== null => "→ {$to}",
            default => '',
        };
    }
}
