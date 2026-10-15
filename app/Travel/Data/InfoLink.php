<?php

namespace App\Travel\Data;

/**
 * The information link of a transit line: a url that may hold variables, {%model_property%}, such as
 * https://www.eurostar.com/nl-nl/reisinformatie/dienstregeling/{%leg_number%}?date={%journey_date%}. Each leg of the
 * line fills them in with its own values, which LegData gathers.
 */
final class InfoLink
{
    /**
     * The variables a link may hold, each with an example value, by which a link is checked as a url.
     */
    public const VARIABLES = [
        'leg_number' => '9416',
        'journey_date' => '2026-08-11',
    ];

    /**
     * A variable, with room for spaces inside the braces: {%leg_number%} or {% leg_number %}.
     */
    private const VARIABLE = '/\{%\s*([^%{}\s]*)\s*%\}/';

    /**
     * Whether the link holds variables, so it only leads somewhere once a leg fills them in.
     */
    public static function hasVariables(string $link): bool
    {
        return preg_match(self::VARIABLE, $link) === 1;
    }

    /**
     * The names of the variables in the link that are not one of VARIABLES.
     *
     * @return list<string>
     */
    public static function unknownVariables(string $link): array
    {
        preg_match_all(self::VARIABLE, $link, $matches);

        return array_values(array_unique(array_diff($matches[1], array_keys(self::VARIABLES))));
    }

    /**
     * The link with its variables filled in, url-encoded. Null when one of them has no value, as a link that points
     * nowhere is worse than none.
     *
     * @param  array<string, string|null>  $values
     */
    public static function fill(string $link, array $values): ?string
    {
        $missing = false;
        $filled = preg_replace_callback(self::VARIABLE, function (array $match) use ($values, &$missing): string {
            $value = $values[$match[1]] ?? null;
            if ($value === null || $value === '') {
                $missing = true;

                return '';
            }

            return rawurlencode($value);
        }, $link);

        return $missing ? null : $filled;
    }
}
