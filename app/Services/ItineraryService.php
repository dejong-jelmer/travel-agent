<?php

namespace App\Services;

use App\Models\Image;
use App\Models\Itinerary;
use App\Models\Trip;

class ItineraryService
{
    /**
     * The itinerary of a trip ready for display on the trip page, in the order set in the admin.
     *
     * Consecutive items with the same accommodation share one accommodation label, given to the first item of that
     * run and counting all days the run covers: an item for days 3-6 counts for 4. The other items of the run and
     * the items without accommodation get null.
     *
     * @return list<array{id: int, type: string, day_from: int, day_to: int|null, title: string, description: string, accommodation_label: string|null, remark: string|null, image: Image|null}>
     */
    public function forDisplay(Trip $trip): array
    {
        $itineraries = $trip->itineraries;
        $accommodationLabels = $this->accommodationLabels($itineraries->all());

        return $itineraries->map(fn (Itinerary $itinerary, int $index) => [
            'id' => $itinerary->id,
            'type' => $itinerary->type->value,
            'day_from' => $itinerary->day_from,
            'day_to' => $itinerary->day_to,
            'title' => $itinerary->title,
            'description' => $itinerary->description,
            'accommodation_label' => $accommodationLabels[$index] ?? null,
            'remark' => $itinerary->remark,
            'image' => $itinerary->image,
        ])->all();
    }

    /**
     * The accommodation label of every run of consecutive items with the same accommodation, keyed by the index of
     * the first item of the run.
     *
     * @param  list<Itinerary>  $itineraries
     * @return array<int, string>
     */
    private function accommodationLabels(array $itineraries): array
    {
        $runs = [];
        $runStart = null;

        foreach ($itineraries as $index => $itinerary) {
            $accommodation = trim((string) $itinerary->accommodation);

            if ($accommodation === '') {
                $runStart = null;

                continue;
            }

            if ($runStart === null || $runs[$runStart]['accommodation'] !== $accommodation) {
                $runStart = $index;
                $runs[$runStart] = ['accommodation' => $accommodation, 'days' => 0];
            }

            $runs[$runStart]['days'] += ($itinerary->day_to ?? $itinerary->day_from) - $itinerary->day_from + 1;
        }

        return array_map(
            fn (array $run) => trans_choice('itinerary.accommodation_label', $run['days'], ['accommodation' => $run['accommodation']]),
            $runs
        );
    }
}
