@extends('emails.default')

@section('content')
    {{-- Preview Text (hidden, only visible in email client inbox preview) --}}
    @if(!empty($campaign->preview_text))
        <div style="font-size:0;line-height:0;max-height:0;max-width:0;opacity:0;overflow:hidden;mso-hide:all;">
            {{ $campaign->preview_text }}
        </div>
    @endif

    {{-- Campaign Title --}}
    <h2 style="margin:0 0 20px;font-size:24px;font-weight:600;color:#30547e;line-height:1.3;">
        {{ $campaign->subject }}
    </h2>

    {{-- Hero Image + Campaign Content --}}
    <div style="color:#1e2d3d;font-size:16px;line-height:1.6;margin-bottom:30px;">
        @if(!empty($heroImage))
            {{-- Outlook-specific table fallback --}}
            <!--[if mso]>
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td width="60%" valign="top" style="padding-right:20px;">
            <![endif]-->

            {{-- Modern email clients: floating image --}}
            <img src="{{ $heroImage }}"
                 alt="Header"
                 style="float:right;width:280px;max-width:45%;height:auto;margin:0 0 20px 20px;border-radius:8px;display:block;">

            {{-- Outlook: close first column, start second --}}
            <!--[if mso]>
                    </td>
                    <td width="40%" valign="top">
            <![endif]-->
        @endif

        {{-- Content flows around the image --}}
        {!! $campaign->content !!}

        @if(!empty($heroImage))
            {{-- Outlook: close table --}}
            <!--[if mso]>
                    </td>
                </tr>
            </table>
            <![endif]-->

            {{-- Clear float for modern clients --}}
            <div style="clear:both;"></div>
        @endif
    </div>

    {{-- Featured Trips Section (optional) --}}
    @if(!empty($featuredTrips) && count($featuredTrips) > 0)
        <h3 style="margin:30px 0 20px;font-size:20px;font-weight:600;color:#30547e;">
            Onze Aanbevolen Reizen
        </h3>

        @foreach($featuredTrips as $trip)
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:20px;border:1px solid #d6e4ef;border-radius:8px;overflow:hidden;">
                <tr>
                    {{-- Trip Image --}}
                    @if(!empty($trip->heroImage?->public_url ?? false))
                    <td width="180" style="padding:0;">
                        <img src="{{ $trip->heroImage?->public_url ?? '' }}" alt="{{ $trip->name }}"
                            style="width:180px;height:135px;object-fit:cover;display:block;">
                    </td>
                    @endif

                    {{-- Trip Info --}}
                    <td style="padding:16px;vertical-align:top;">
                        <h4 style="margin:0 0 8px;font-size:17px;font-weight:600;color:#30547e;">
                            {{ $trip->name }}
                        </h4>
                        <p style="margin:0 0 12px;color:#a3bccb;font-size:14px;line-height:1.4;">
                            {{ $trip->description }}
                        </p>
                        <a href="{{ $trip->url }}"
                           style="display:inline-block;padding:8px 18px;background:#f59e0b;color:#FFFFFF;text-decoration:none;border-radius:6px;font-size:14px;font-weight:500;">
                            Bekijk Reis
                        </a>
                    </td>
                </tr>
            </table>
        @endforeach
    @endif

    {{-- Call to Action Button (optional) --}}
    @if(!empty($ctaText) && !empty($ctaUrl))
        <div style="margin:30px 0;text-align:center;">
            <a href="{{ $ctaUrl }}"
               style="display:inline-block;padding:14px 36px;background:#b17c65;color:#FFFFFF;text-decoration:none;border-radius:8px;font-size:16px;font-weight:600;letter-spacing:0.3px;">
                {{ $ctaText }}
            </a>
        </div>
    @endif

    {{-- Divider --}}
    <div style="margin:30px 0;border-top:1px solid #d6e4ef;"></div>

    {{-- Social Links (optional) --}}
    @if(!empty($socialLinks))
        <div style="text-align:center;margin-bottom:20px;">
            @foreach($socialLinks as $platform => $url)
                <a href="{{ $url }}" style="display:inline-block;margin:0 6px;">
                    <img src="{{ asset('images/social/' . $platform . '.png') }}"
                         alt="{{ ucfirst($platform) }}"
                         style="width:28px;height:28px;display:inline-block;">
                </a>
            @endforeach
        </div>
    @endif

    {{-- Company Info --}}
    <p style="margin:0 0 16px;text-align:center;color:#a3bccb;font-size:13px;line-height:1.5;">
        <strong>{{ config('app.name') }}</strong><br>
        @if(!empty(config('contact.address')))
            {{ config('contact.address') }}<br>
        @endif
        @if(!empty(config('contact.email')))
            <a href="mailto:{{ config('contact.email') }}" style="color:#82b2ca;text-decoration:none;">
                {{ config('contact.email') }}
            </a>
        @endif
    </p>

    {{-- Unsubscribe Link --}}
    <p style="margin:0;text-align:center;color:#a3bccb;font-size:11px;line-height:1.5;">
        Je ontvangt deze email omdat je bent ingeschreven op onze nieuwsbrief.<br>
        <a href="{{ $unsubscribeUrl ?? route('newsletter.subscription.unsubscribe', $subscriber->unsubscribe_token ?? '') }}"
           style="color:#82b2ca;text-decoration:underline;">
            Uitschrijven
        </a>
    </p>
@endsection
