<?php

namespace App\Actions;

use Illuminate\Support\Collection;
use Spatie\Regex\Regex;

class FlightPlanParser
{
    public static function process(string $flightPlanText): Collection
    {
        return (new self())->parse($flightPlanText);
    }

    /**
     * @return Collection{
     *     departureAirport: string,
     *     destinationAirport: string,
     *     destinationAlternates: array<string>,
     *     firs: array<string>,
     *     enrouteAlternates: array<string>,
     *     takeoffAlternate: string,
     * }
     */
    public function parse($flightPlanText): Collection
    {
        $flightplan = collect();

        //Split the flightplan into sections. Each section is marked with a dash at start of the line
        $fields = str($flightPlanText)->split('/^ *?-/m')->toArray();

        //Extract slices of text that are required from some of those sections.
        $firs = Regex::match('/EET\/([\w\s]*)\s{1}[A-Z]+\//i', $fields[5])->groupOr(1, '');
        $enrAlt = Regex::match('/RALT\/([\w\s]*)\s{1}[A-Z]+\//i', $fields[5])->groupOr(1, '');
        $takeOffAlt = Regex::match('/TALT\/([\w\s]*)\s{1}[A-Z]+\//', $fields[5])->groupOr(1, '');

        //For some fields that have multiple airports, pull them into arrays.
        preg_match_all('/[A-Z]{4}/i', $fields[4], $destMatches, PREG_PATTERN_ORDER);
        preg_match_all('/\b([A-Z]{4})[0-9]{4}/i', $firs, $firMatches, PREG_PATTERN_ORDER);
        preg_match_all('/\b[A-Z]{4}/i', $enrAlt, $enrAltMatches, PREG_PATTERN_ORDER);

        //Populate our array with all the data.
        $flightplan['departureAirport'] = Regex::match('/[A-Z]{4}/i', $fields[2])->resultOr('');
        $flightplan['destinationAirport'] = array_shift($destMatches[0]);
        $flightplan['destinationAlternates'] = $destMatches[0];
        $flightplan['firs'] = array_values(array_unique($firMatches[1]));
        $flightplan['enrouteAlternates'] = $enrAltMatches[0] ?? [];
        $flightplan['takeoffAlternate'] = Regex::match('/[A-Z]{4}/i', $takeOffAlt)->resultOr('');

        return $flightplan;
    }
}
