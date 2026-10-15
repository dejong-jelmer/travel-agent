@extends('emails.default')

@php
    $travelerCount = $booking->travelers->count();
    $isSolo = $travelerCount === 1;
@endphp

@section('preheader')
    Je boeking voor {{ $booking->trip->name }} is binnen. Boekingsnummer {{ $booking->reference }}.
@endsection

@section('content')
    {{-- Greeting --}}
    <p style="margin:0 0 20px 0;font-size:15px;line-height:1.7;color:#1e2d3d;">
        Beste {{ $booking->mainBooker->first_name }},
    </p>

    <h1 style="margin:0 0 16px 0;font-family:Georgia,'Times New Roman',serif;font-size:26px;line-height:1.3;font-weight:normal;color:#1e2d3d;">
        Je boeking is binnen
    </h1>

    <p style="margin:0 0 32px 0;font-size:15px;line-height:1.7;color:#1e2d3d;">
        Wat leuk dat {{ $isSolo ? 'je' : 'jullie' }} met {{ config('app.name') }} op pad {{ $isSolo ? 'gaat' : 'gaan' }}.
        Hieronder vind je een overzicht van je boeking. Ik ga er nu voor je mee aan de slag.
    </p>

    {{-- Booking reference --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f5f0e8;">
        <tr>
            <td style="padding:22px 24px;">
                <p style="margin:0 0 4px 0;font-size:13px;line-height:1.5;color:#4d6f80;">
                    Je boekingsnummer
                </p>
                <p style="margin:0;font-family:Georgia,'Times New Roman',serif;font-size:24px;line-height:1.3;letter-spacing:1px;color:#2d5f6e;">
                    {{ $booking->reference }}
                </p>
                <p style="margin:8px 0 0 0;font-size:13px;line-height:1.5;color:#4d6f80;">
                    Handig voor de administratie of voor als we contact hebben.
                </p>
            </td>
        </tr>
    </table>

    {{-- Trip details --}}
    @include('emails.partials.heading', ['text' => 'Je reis'])

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
        @include('emails.partials.detail-row', ['label' => 'Reis', 'value' => $booking->trip->name, 'strong' => true])
        @include('emails.partials.detail-row', ['label' => 'Vertrek', 'value' => $booking->departure_date_formatted])
        @include('emails.partials.detail-row', ['label' => 'Terugkomst', 'value' => $booking->return_date_formatted])
        @include('emails.partials.detail-row', ['label' => 'Duur', 'value' => $booking->trip->duration . ' dagen'])
    </table>

    {{-- Travelers --}}
    @include('emails.partials.heading', ['text' => 'Reizigers'])

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
        @foreach ($booking->travelers as $index => $traveler)
            <tr>
                <td width="32" style="padding:12px 0;border-bottom:1px solid #eee8dc;vertical-align:top;font-family:Georgia,'Times New Roman',serif;font-size:15px;line-height:1.5;color:#2d5f6e;">
                    {{ $index + 1 }}
                </td>
                <td style="padding:12px 0;border-bottom:1px solid #eee8dc;vertical-align:top;font-size:15px;line-height:1.5;color:#1e2d3d;">
                    {{ $traveler->full_name }}
                    @if ($traveler->id === $booking->main_booker_id)
                        <span style="font-size:13px;color:#4d6f80;">&nbsp;&middot;&nbsp;hoofdboeker</span>
                    @endif
                    <br>
                    <span style="font-size:13px;color:#4d6f80;">Geboren op {{ $traveler->birthdate_formatted }}</span>
                </td>
            </tr>
        @endforeach
    </table>

    {{-- Contact details --}}
    @include('emails.partials.heading', ['text' => 'Je contactgegevens'])

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
        @include('emails.partials.detail-row', ['label' => 'Naam', 'value' => $booking->contact->name])
        @include('emails.partials.detail-row', ['label' => 'E-mailadres', 'value' => $booking->contact->email])
        @include('emails.partials.detail-row', ['label' => 'Telefoonnummer', 'value' => $booking->contact->phone])
        @include('emails.partials.detail-row', ['label' => 'Adres', 'value' => $booking->contact->address, 'multiline' => true])
    </table>

    <p style="margin:12px 0 0 0;font-size:13px;line-height:1.6;color:#4d6f80;">
        Klopt er iets niet? Antwoord gewoon op deze e-mail, dan pas ik het aan.
    </p>

    {{-- Next steps --}}
    @include('emails.partials.heading', ['text' => 'Wat gebeurt er nu?'])

    @php
        $steps = [
            ['Ik ga aan de slag', 'Je boeking is bij mij binnen en ik ga alles voor je vastleggen.'],
            ['Je hoort van mij', 'Binnen drie werkdagen neem ik contact met je op voor de definitieve bevestiging en om eventuele vragen door te nemen.'],
            ['Je reisdocumenten', 'Uiterlijk twee weken voor vertrek ontvang je alles wat je nodig hebt: tickets, routebeschrijving en mijn persoonlijke tips voor onderweg.'],
        ];
    @endphp

    @include('emails.partials.steps', ['steps' => $steps])

    {{-- Contact --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:16px 0 0 0;border-left:3px solid #afcb98;">
        <tr>
            <td style="padding:4px 0 4px 20px;font-size:14px;line-height:1.7;color:#1e2d3d;">
                Heb je nu al een vraag? Bel of mail me gerust.<br>
                <a href="tel:{{ config('contact.phone') }}" style="color:#2d5f6e;text-decoration:none;">{{ config('contact.phone') }}</a>
                &nbsp;&middot;&nbsp;
                <a href="mailto:{{ config('contact.mail') }}" style="color:#2d5f6e;text-decoration:none;">{{ config('contact.mail') }}</a>
            </td>
        </tr>
    </table>

    {{-- Closing --}}
    <p style="margin:36px 0 0 0;font-size:15px;line-height:1.7;color:#1e2d3d;">
        Hartelijke groet,
    </p>
    <p style="margin:4px 0 0 0;font-size:15px;line-height:1.5;color:#1e2d3d;">
        <span style="font-family:Georgia,'Times New Roman',serif;font-size:18px;">{{ config('contact.first_name') }}</span><br>
        <span style="font-size:13px;color:#4d6f80;">{{ config('app.name') }}</span>
    </p>
@endsection
