<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SettingKey;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasPageMetadata;
use App\Http\Requests\UpdateSettingsRequest;
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

        return redirect()->route('admin.settings.edit')
            ->with('success', __('setting.updated'));
    }
}
