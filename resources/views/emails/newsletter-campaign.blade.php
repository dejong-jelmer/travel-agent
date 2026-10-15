@extends('emails.default')

@if (!empty($campaign->preview_text))
    @section('preheader')
        {{ $campaign->preview_text }}
    @endsection
@endif

@section('content')
    {{-- Hero image: full width above the title, calmer and more robust than a floating image --}}
    @if (!empty($heroImage))
        <img src="{{ $heroImage }}" alt="" width="504"
            style="display:block;width:100%;max-width:504px;height:auto;margin:0 0 32px 0;border:0;">
    @endif

    {{-- Campaign title --}}
    <h1 style="margin:0 0 24px 0;font-family:Georgia,'Times New Roman',serif;font-size:26px;line-height:1.3;font-weight:normal;color:#1e2d3d;">
        {{ $campaign->subject }}
    </h1>

    {{-- Campaign content from the editor, styled through .email-prose in the layout --}}
    <div class="email-prose" style="font-size:16px;line-height:1.7;color:#1e2d3d;">
        {!! $campaign->content !!}
    </div>

    {{-- Featured trips (optional) --}}
    @if (!empty($featuredTrips) && count($featuredTrips) > 0)
        @include('emails.partials.heading', ['text' => count($featuredTrips) === 1 ? 'De reis' : 'De reizen'])

        @foreach ($featuredTrips as $trip)
            @php
                // Clip the plain text description so every trip card keeps a similar height.
                $tripDescription = Str::limit($trip->description_plain, 160);
                $tripUrl = route('trips.show', $trip->slug);
                $tripImage = $trip->heroImage?->public_url;
            @endphp
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                style="margin:0 0 8px 0;border-bottom:1px solid #eee8dc;">
                <tr>
                    @if (!empty($tripImage))
                        <td width="168" class="email-stack email-stack-img" style="padding:16px 20px 24px 0;vertical-align:top;">
                            <a href="{{ $tripUrl }}">
                                <img src="{{ $tripImage }}" alt="{{ $trip->name }}" width="168" height="126"
                                    style="display:block;width:168px;height:126px;object-fit:cover;border:0;">
                            </a>
                        </td>
                    @endif
                    <td class="email-stack" style="padding:16px 0 24px 0;vertical-align:top;">
                        <p style="margin:0 0 6px 0;font-family:Georgia,'Times New Roman',serif;font-size:18px;line-height:1.3;color:#1e2d3d;">
                            {{ $trip->name }}
                        </p>
                        <p style="margin:0 0 12px 0;font-size:14px;line-height:1.6;color:#4d6f80;">
                            {{ $tripDescription }}
                        </p>
                        <a href="{{ $tripUrl }}" style="font-size:14px;font-weight:bold;color:#2d5f6e;text-decoration:none;">
                            Bekijk reis &rarr;
                        </a>
                    </td>
                </tr>
            </table>
        @endforeach
    @endif

    {{-- Call to action (optional) --}}
    @if (!empty($ctaText) && !empty($ctaUrl))
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td align="center" style="padding:36px 0 8px 0;">
                    @include('emails.partials.button', ['url' => $ctaUrl, 'text' => $ctaText])
                </td>
            </tr>
        </table>
    @endif

    {{-- Signature --}}
    <p style="margin:40px 0 0 0;font-size:15px;line-height:1.5;color:#1e2d3d;">
        <span style="font-family:Georgia,'Times New Roman',serif;font-size:18px;">{{ config('contact.first_name') }}</span><br>
        <span style="font-size:13px;color:#4d6f80;">{{ config('app.name') }}</span>
    </p>
@endsection

@section('footer')
    {{-- Social links (optional) --}}
    @if (!empty($socialLinks))
        <p style="margin:0 0 12px 0;">
            @foreach ($socialLinks as $platform => $url)
                <a href="{{ $url }}" style="display:inline-block;margin:0 6px;">
                    <img src="{{ asset('images/social/' . $platform . '.png') }}" alt="{{ ucfirst($platform) }}"
                        width="24" height="24" style="display:inline-block;width:24px;height:24px;border:0;">
                </a>
            @endforeach
        </p>
    @endif

    <p style="margin:0;font-size:12px;line-height:1.7;color:#4d6f80;">
        Je ontvangt deze e-mail omdat je je hebt ingeschreven voor de nieuwsbrief.<br>
        <a href="{{ $unsubscribeUrl ?? route('newsletter.subscription.unsubscribe', $subscriber->unsubscribe_token ?? '') }}"
            style="color:#4d6f80;text-decoration:underline;">Uitschrijven</a>
    </p>
@endsection
