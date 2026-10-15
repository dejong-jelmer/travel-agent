<?php

namespace App\Travel\Database\Factories;

use App\Models\Booking;
use App\Travel\Models\ShareLink;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ShareLink>
 */
class ShareLinkFactory extends Factory
{
    protected $model = ShareLink::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory(),
            'token' => Str::random(40),
        ];
    }
}
