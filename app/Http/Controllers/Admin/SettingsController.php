<?php

namespace App\Http\Controllers\Admin;

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
                'booking_season_end' => Setting::get('booking_season_end'),
            ],
        ]);
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        Setting::set('booking_season_end', $validated['booking_season_end'] ?? null);

        return redirect()->route('admin.settings.edit')
            ->with('success', __('setting.updated'));
    }
}
