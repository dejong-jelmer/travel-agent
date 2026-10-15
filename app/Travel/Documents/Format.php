<?php

namespace App\Travel\Documents;

use Carbon\CarbonInterface;

/**
 * How the documents write dates, times, durations and contact details: in Dutch, as the design writes them, and as
 * wall-clock times at the place itself.
 */
class Format
{
    /**
     * A transfer this short gets a warning.
     */
    public const SHORT_TRANSFER_MINUTES = 15;

    /**
     * 'dinsdag 11 augustus'
     */
    public static function date(?CarbonInterface $date): string
    {
        return $date?->locale('nl')->isoFormat('dddd D MMMM') ?? '';
    }

    /**
     * 'DI', '11' and 'aug', for the day tile of a journey.
     *
     * @return array{weekday: string, day: string, month: string}
     */
    public static function day(CarbonInterface $date): array
    {
        $date = $date->locale('nl');

        return [
            'weekday' => mb_strtoupper(str_replace('.', '', $date->isoFormat('dd'))),
            'day' => $date->isoFormat('D'),
            'month' => str_replace('.', '', $date->isoFormat('MMM')),
        ];
    }

    /**
     * '11 – 16 augustus 2026', '30 juli – 2 augustus 2026' or '30 december 2026 – 2 januari 2027'.
     */
    public static function period(?CarbonInterface $first, ?CarbonInterface $last): string
    {
        if ($first === null || $last === null) {
            return '';
        }

        $first = $first->locale('nl');
        $last = $last->locale('nl');

        return match (true) {
            $first->isSameDay($last) => $last->isoFormat('D MMMM YYYY'),
            $first->isSameMonth($last) => $first->isoFormat('D').' – '.$last->isoFormat('D MMMM YYYY'),
            $first->isSameYear($last) => $first->isoFormat('D MMMM').' – '.$last->isoFormat('D MMMM YYYY'),
            default => $first->isoFormat('D MMMM YYYY').' – '.$last->isoFormat('D MMMM YYYY'),
        };
    }

    /**
     * '11 – 14 aug' or '30 jul – 2 aug', for a stay.
     */
    public static function shortPeriod(?CarbonInterface $first, ?CarbonInterface $last): string
    {
        if ($first === null || $last === null) {
            return '';
        }

        $month = fn (CarbonInterface $date) => str_replace('.', '', $date->locale('nl')->isoFormat('MMM'));

        return $first->isSameMonth($last)
            ? "{$first->day} – {$last->day} {$month($last)}"
            : "{$first->day} {$month($first)} – {$last->day} {$month($last)}";
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
     * '54 min', '1u00', '3u07'
     */
    public static function duration(?int $minutes): string
    {
        if ($minutes === null) {
            return '';
        }

        return $minutes < 60 ? "{$minutes} min" : sprintf('%du%02d', intdiv($minutes, 60), $minutes % 60);
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
     * 'direct', '1 overstap', '5 overstappen'
     */
    public static function changes(int $changes): string
    {
        return match ($changes) {
            0 => 'direct',
            1 => '1 overstap',
            default => "{$changes} overstappen",
        };
    }

    /**
     * 'Frankrijk', 'Frankrijk en Spanje', 'Frankrijk, Spanje en Portugal'
     *
     * @param  list<string>  $names
     */
    public static function list(array $names): string
    {
        $last = array_pop($names);

        return $names === [] ? (string) $last : implode(', ', $names).' en '.$last;
    }

    /**
     * The street and the place of an address, which the documents set on two lines: '54 Avenue de la Préhistoire'
     * and '24620 Les Eyzies-de-Tayac-Sireuil'.
     *
     * @return list<string>
     */
    public static function addressLines(string $address): array
    {
        return array_map('trim', explode(',', $address, 2));
    }

    /**
     * The address as a link that opens in a map.
     */
    public static function mapUrl(string $address): string
    {
        return 'https://maps.google.com/?q='.urlencode($address);
    }

    /**
     * The number to dial: '+33 (0)5 53 06 97 06' becomes 'tel:+33553069706'.
     */
    public static function phoneUrl(string $phone): string
    {
        return 'tel:'.preg_replace('/[^\d+]/', '', str_replace('(0)', '', $phone));
    }

    /**
     * Walking directions as steps, one per sentence or line.
     *
     * @return list<string>
     */
    public static function steps(?string $directions): array
    {
        if ($directions === null || trim($directions) === '') {
            return [];
        }

        return array_values(array_filter(
            array_map('trim', preg_split('/(?<=[.!?])\s+|\R+/u', trim($directions)) ?: []),
            fn (string $step) => $step !== '',
        ));
    }

    /**
     * The short name on the badge of a line: 'Lijn 4' gives '4', 'Tram C' gives 'C', a longer last word its first
     * letter.
     */
    public static function lineBadge(string $name): string
    {
        $words = preg_split('/\s+/', trim($name)) ?: [$name];
        $last = (string) end($words);

        return mb_strlen($last) <= 3 ? $last : mb_substr($last, 0, 1);
    }
}
