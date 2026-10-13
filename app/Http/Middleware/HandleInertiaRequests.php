<?php

namespace App\Http\Middleware;

use App\Services\ContactDetailsService;
use Closure;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

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
     * Turns server-side rendering off for the requests that do not need it, before the parent middleware runs.
     *
     * @see https://inertiajs.com/server-side-rendering
     */
    public function handle(Request $request, Closure $next)
    {
        if (! $this->ssrEnabled($request)) {
            config(['inertia.ssr.enabled' => false]);
        }

        return parent::handle($request, $next);
    }

    /**
     * Whether the first visit of the page is rendered on the SSR server (`php artisan inertia:start-ssr`). Admin pages
     * are not: they sit behind a login, so search engines never see them, and their editors only work in a browser.
     */
    protected function ssrEnabled(Request $request): bool
    {
        return config('inertia.ssr.enabled') && ! $request->is('admin*');
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
            'locale' => app()->getLocale(),
            'locales' => availableLocales() ?: ['nl', 'en'],
            // The route list for route() on the SSR server, which has no @routes script. Only a first visit is
            // rendered there; the Inertia visits that follow run in the browser and leave it out.
            'ziggy' => fn () => $request->inertia() || ! config('inertia.ssr.enabled') ? null : [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'auth' => [
                'user' => $request->user()?->only(['id', 'name', 'role']) ?: null,
            ],
            'config' => [
                'seo' => config('seo'),
                'images' => config('images'),
            ],
            'contact' => [
                'fullName' => function () use ($contactService) {
                    return $contactService->getContact('full_name');
                },
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

    /**
     * Inertia hook: whether to encrypt the browser history state.
     * Enabled in production to prevent sensitive prop data from being readable in browser history.
     *
     * @see https://inertiajs.com/history-encryption
     */
    public function encryptHistory(): bool
    {
        return ! app()->isLocal();
    }
}
