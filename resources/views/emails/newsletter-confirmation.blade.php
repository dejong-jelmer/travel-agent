@extends('emails.default')

@section('preheader')
    Nog één klik: bevestig je inschrijving voor de nieuwsbrief van {{ config('app.name') }}.
@endsection

@section('content')
    {{-- Greeting --}}
    <p style="margin:0 0 20px 0;font-size:15px;line-height:1.7;color:#1e2d3d;">
        Hallo {{ $subscriber->name ?? strtok($subscriber->email, '@') }},
    </p>

    <h1 style="margin:0 0 16px 0;font-family:Georgia,'Times New Roman',serif;font-size:26px;line-height:1.3;font-weight:normal;color:#1e2d3d;">
        Welkom bij de nieuwsbrief
    </h1>

    <p style="margin:0 0 16px 0;font-size:15px;line-height:1.7;color:#1e2d3d;">
        Wat leuk dat je erbij bent. Ik bouw {{ config('app.name') }} op als reismaker die de bestemming als
        uitgangspunt neemt, met aandacht voor de plek en het verhaal. Via deze nieuwsbrief deel ik wat ik
        onderweg tegenkom en waar ik mee bezig ben.
    </p>

    <p style="margin:0 0 32px 0;font-size:15px;line-height:1.7;color:#1e2d3d;">
        Je hebt je ingeschreven met <strong style="font-weight:bold;">{{ $subscriber->email }}</strong>.
        Bevestig je inschrijving via de knop hieronder, dan ben je er helemaal bij.
    </p>

    {{-- Confirmation button --}}
    @include('emails.partials.button', [
        'url' => route('newsletter.subscription.confirm', $subscriber->confirmation_token),
        'text' => 'Inschrijving bevestigen',
    ])

    <p style="margin:14px 0 0 0;text-align:center;font-size:13px;line-height:1.5;color:#4d6f80;">
        Deze link is {{ config('newsletter.subscription.confirmation_expires_after') }} uur geldig.
    </p>

    {{-- What to expect --}}
    @include('emails.partials.heading', ['text' => 'Wat kun je verwachten?'])

    @php
        $expectations = [
            'Nieuwe reizen die ik samenstel.',
            'Persoonlijke ervaringen en verhalen van onderweg.',
            'Achtergrond bij bestemmingen.',
        ];
    @endphp

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
        @foreach ($expectations as $expectation)
            <tr>
                <td width="24" style="padding:0 0 10px 0;vertical-align:top;font-size:15px;line-height:1.6;color:#afcb98;">
                    &#9679;
                </td>
                <td style="padding:0 0 10px 0;vertical-align:top;font-size:15px;line-height:1.6;color:#1e2d3d;">
                    {{ $expectation }}
                </td>
            </tr>
        @endforeach
    </table>

    {{-- Privacy --}}
    <p style="margin:24px 0 0 0;font-size:13px;line-height:1.7;color:#4d6f80;">
        Ik gebruik je e-mailadres alleen voor de nieuwsbrief en deel het nooit met anderen.
        Je kunt je altijd weer
        <a href="{{ route('newsletter.subscription.unsubscribe', $subscriber->unsubscribe_token) }}"
            style="color:#4d6f80;text-decoration:underline;">uitschrijven</a>.
    </p>

    {{-- Closing --}}
    <p style="margin:36px 0 0 0;font-size:15px;line-height:1.7;color:#1e2d3d;">
        Hartelijke groet,
    </p>
    <p style="margin:4px 0 0 0;font-size:15px;line-height:1.5;color:#1e2d3d;">
        <span style="font-family:Georgia,'Times New Roman',serif;font-size:18px;">{{ config('contact.first_name') }}</span><br>
        <span style="font-size:13px;color:#4d6f80;">{{ config('app.name') }}</span>
    </p>
@endsection
