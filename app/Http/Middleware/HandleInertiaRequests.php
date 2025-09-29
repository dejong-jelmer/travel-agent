<?php

namespace App\Http\Middleware;

use App\Services\ContactDetailsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $contactService = app(ContactDetailsService::class);

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => Auth::user(),
            ],
            'settings' => Config::get('app-settings'),
            'contact' => [
                'phone' => function () use ($contactService) {
                    return $contactService->getContact('phone')->getPhoneNumber();
                },
                'fullAddress' => function () use ($contactService) {
                    return $contactService->fullAddress();
                },
                'mail' => function () use ($contactService) {
                    return [
                        'link' => $contactService->getContact('mail')->forMailtoLink(),
                        'display' => $contactService->getContact('mail')->forDisplay(),
                    ];
                },
                'mapsLink' => function () use ($contactService) {
                    return $contactService->getContact('mapsLink');
                },
                'kvk' => function () use ($contactService) {
                    return $contactService->getContact('kvk');
                },
                'btw' => function () use ($contactService) {
                    return $contactService->getContact('btw');
                },
            ],
        ]);
    }
}
