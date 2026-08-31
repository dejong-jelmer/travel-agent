<?php

namespace Tests\Unit;

use App\Models\Booking;
use App\Models\BookingCostItem;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Runs the shared price vectors against the PHP implementation.
 *
 * The same vectors run against calculateBookingPrice() in
 * resources/js/__tests__/Support/bookingPrice.test.js. If you change the
 * formula here without changing it there, that test fails — and the other
 * way around.
 */
class BookingPriceVectorsTest extends TestCase
{
    private const FIXTURE = __DIR__.'/../fixtures/booking-price-vectors.json';

    #[DataProvider('vectors')]
    public function test_the_price_matches_the_shared_vector(array $vector): void
    {
        $booking = $this->bookingFor($vector);

        $this->assertSame(
            $vector['expected']['total_cost'],
            $booking->total_cost,
            'total cost',
        );

        $this->assertSame(
            $vector['expected']['fees_and_funds_total'],
            $booking->fees_and_funds_total,
            'fees and funds total',
        );

        $this->assertSame(
            $vector['expected']['calculated_price'],
            $booking->computeCalculatedPrice(),
            'calculated price',
        );

        // The margined price is private, so pin it through the public result.
        $this->assertSame(
            $vector['expected']['margined_price'],
            $booking->computeCalculatedPrice() - $booking->fees_and_funds_total,
            'margined price',
        );
    }

    public function test_the_vectors_cover_both_margin_modes(): void
    {
        $modes = array_unique(array_column(
            array_column(iterator_to_array(self::vectors()), 0),
            'margin_in_percentage',
        ));

        $this->assertEqualsCanonicalizing([true, false], array_values($modes));
    }

    /**
     * @return iterable<string, array{array<string, mixed>}>
     */
    public static function vectors(): iterable
    {
        $fixture = json_decode(file_get_contents(self::FIXTURE), true, 512, JSON_THROW_ON_ERROR);

        foreach ($fixture['vectors'] as $vector) {
            yield $vector['name'] => [$vector];
        }
    }

    private function bookingFor(array $vector): Booking
    {
        $booking = new Booking([
            'margin_in_percentage' => $vector['margin_in_percentage'],
            'margin_basis_points' => (int) round($vector['margin_percentage'] * 100),
            'margin_amount' => $vector['margin_amount'],
            'fee_per_person' => $vector['fee_per_person'],
            'total_adults' => $vector['adults'],
            'fees_and_funds' => $vector['fees_and_funds'],
        ]);

        $booking->setRelation('costItems', Collection::make(array_map(
            fn (array $item) => new BookingCostItem($item),
            $vector['cost_items'],
        )));

        return $booking;
    }
}
