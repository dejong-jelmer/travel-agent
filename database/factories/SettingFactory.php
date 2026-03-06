<?php

namespace Database\Factories;

use App\Enums\SettingKey;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Setting>
 */
class SettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => SettingKey::BookingFee->value,
            'value' => '25.00',
        ];
    }

    public function bookingFee(string $value = '25.00'): static
    {
        return $this->state([
            'key' => SettingKey::BookingFee->value,
            'value' => $value,
        ]);
    }

    public function guaranteeFund(string $value = '10.00'): static
    {
        return $this->state([
            'key' => SettingKey::GuaranteeFund->value,
            'value' => $value,
        ]);
    }

    public function emergencyFund(string $value = '0.00'): static
    {
        return $this->state([
            'key' => SettingKey::EmergencyFund->value,
            'value' => $value,
        ]);
    }

    public function bookingSeasonEnd(?string $date = null): static
    {
        return $this->state([
            'key' => SettingKey::BookingSeasonEnd->value,
            'value' => $date ?? now()->addYear()->format('Y-m-d'),
        ]);
    }
}
