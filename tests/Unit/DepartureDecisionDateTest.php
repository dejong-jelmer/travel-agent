<?php

namespace Tests\Unit;

use App\Models\Departure;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class DepartureDecisionDateTest extends TestCase
{
    /**
     * The duration counts the day of departure and the day of return.
     *
     * @return array<string, array{string, string, int, string}>
     */
    public static function trips(): array
    {
        return [
            'day trip: 48 hours' => ['2027-06-20', '2027-06-20', 1, '2027-06-18'],
            '2 days: 7 days' => ['2027-06-20', '2027-06-21', 2, '2027-06-13'],
            '6 days: 7 days' => ['2027-06-20', '2027-06-25', 6, '2027-06-13'],
            '7 days: 20 days' => ['2027-06-20', '2027-06-26', 7, '2027-05-31'],
            '15 days: 20 days' => ['2027-06-20', '2027-07-04', 15, '2027-05-31'],
            'across a year boundary' => ['2028-01-05', '2028-01-14', 10, '2027-12-16'],
        ];
    }

    #[DataProvider('trips')]
    public function test_latest_decision_date_follows_the_duration_of_the_trip(string $departsOn, string $returnsOn, int $duration, string $expected): void
    {
        $departure = new Departure(['departs_on' => $departsOn, 'returns_on' => $returnsOn]);

        $this->assertSame($duration, $departure->durationInDays());
        $this->assertSame($expected, $departure->latestDecisionDate()->toDateString());
    }

    public function test_latest_decision_date_ignores_the_time_of_day(): void
    {
        $date = Departure::latestDecisionDateFor(
            Carbon::parse('2027-06-20 23:30'),
            Carbon::parse('2027-06-26 00:15'),
        );

        $this->assertSame('2027-05-31 00:00:00', $date->toDateTimeString());
    }

    public function test_latest_decision_date_does_not_change_the_dates_it_is_given(): void
    {
        $departsOn = Carbon::parse('2027-06-20');
        $returnsOn = Carbon::parse('2027-06-26');

        Departure::latestDecisionDateFor($departsOn, $returnsOn);

        $this->assertSame('2027-06-20', $departsOn->toDateString());
        $this->assertSame('2027-06-26', $returnsOn->toDateString());
    }

    public function test_deadlines_come_from_the_config(): void
    {
        config(['departures.decision_deadlines' => [
            ['min_duration_days' => 4, 'days_before_departure' => 30],
            ['min_duration_days' => 0, 'days_before_departure' => 3],
        ]]);

        $this->assertSame('2027-05-21', Departure::latestDecisionDateFor(Carbon::parse('2027-06-20'), Carbon::parse('2027-06-23'))->toDateString());
        $this->assertSame('2027-06-17', Departure::latestDecisionDateFor(Carbon::parse('2027-06-20'), Carbon::parse('2027-06-22'))->toDateString());
    }
}
