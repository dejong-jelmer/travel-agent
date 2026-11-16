<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SettingKey;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasPageMetadata;
use App\Http\Requests\UpdateSettingsRequest;
use App\Models\Booking;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    use HasPageMetadata;

    public function edit(): Response
    {
        return Inertia::render('Admin/Settings/Edit', [
            'title' => $this->pageTitle('setting.title_edit'),
            'settings' => [
                SettingKey::BookingSeasonEnd->value => Setting::get(SettingKey::BookingSeasonEnd),
                SettingKey::BookingFee->value => Setting::get(SettingKey::BookingFee),
                SettingKey::GuaranteeFund->value => Setting::get(SettingKey::GuaranteeFund),
                SettingKey::EmergencyFund->value => Setting::get(SettingKey::EmergencyFund),
                // Stored in basis points, edited as a percentage.
                'default_booking_margin_percentage' => (int) Setting::get(
                    SettingKey::DefaultBookingMarginBasisPoints,
                    Booking::DEFAULT_MARGIN_BASIS_POINTS
                ) / 100,
                SettingKey::DefaultBookingMarginAmount->value => Setting::get(SettingKey::DefaultBookingMarginAmount),
                SettingKey::DefaultBookingFeePerPerson->value => Setting::get(SettingKey::DefaultBookingFeePerPerson),
            ],
        ]);
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        Setting::set(SettingKey::BookingSeasonEnd, $validated[SettingKey::BookingSeasonEnd->value] ?? null);
        Setting::set(SettingKey::BookingFee, $validated[SettingKey::BookingFee->value] ?? null);
        Setting::set(SettingKey::GuaranteeFund, $validated[SettingKey::GuaranteeFund->value] ?? null);
        Setting::set(SettingKey::EmergencyFund, $validated[SettingKey::EmergencyFund->value] ?? null);

        $marginPercentage = $validated['default_booking_margin_percentage'] ?? null;
        Setting::set(
            SettingKey::DefaultBookingMarginBasisPoints,
            $marginPercentage === null ? null : (int) round(((float) $marginPercentage) * 100),
        );
        Setting::set(SettingKey::DefaultBookingMarginAmount, $validated[SettingKey::DefaultBookingMarginAmount->value] ?? null);
        Setting::set(SettingKey::DefaultBookingFeePerPerson, $validated[SettingKey::DefaultBookingFeePerPerson->value] ?? null);

        return redirect()->route('admin.settings.edit')
            ->with('success', __('setting.updated'));
    }
}
