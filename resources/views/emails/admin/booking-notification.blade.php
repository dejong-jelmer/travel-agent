@extends('emails.default')

@php
    $adminUrl = route('admin.bookings.edit', $booking);
    $travelerCount = $booking->travelers->count();
    $adultCount = $booking->adults->count();
    $childCount = $booking->children->count();
    $statusLabel = $booking->status instanceof \UnitEnum && method_exists($booking->status, 'label')
        ? $booking->status->label()
        : null;
@endphp

@section('preheader')
    {{ $booking->mainBooker->full_name }} heeft {{ $booking->trip->name }} geboekt. Boekingsnummer {{ $booking->reference }}.
@endsection

@section('content')
    @include('emails.partials.admin-intro', [
        'label' => 'Nieuwe boeking',
        'title' => $booking->trip->name,
        'meta' => 'Aangemaakt op ' . $booking->created_at_formatted . ' uur',
        'tone' => 'warning',
    ])

    {{-- Booking reference --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:28px 0 0 0;background:#f5f0e8;">
        <tr>
            <td style="padding:22px 24px;">
                <p style="margin:0 0 4px 0;font-size:13px;line-height:1.5;color:#4d6f80;">
                    Boekingsnummer
                </p>
                <p style="margin:0;font-family:Georgia,'Times New Roman',serif;font-size:24px;line-height:1.3;letter-spacing:1px;color:#2d5f6e;">
                    {{ $booking->reference }}
                </p>
                <p style="margin:8px 0 0 0;font-size:13px;line-height:1.5;">
                    <a href="{{ $adminUrl }}" style="color:#2d5f6e;font-weight:bold;text-decoration:none;">Openen in admin panel &rarr;</a>
                </p>
            </td>
        </tr>
    </table>

    {{-- Trip --}}
    @include('emails.partials.heading', ['text' => 'Reis'])

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
        @include('emails.partials.detail-row', [
            'label' => 'Reis',
            'value' => $booking->trip->name,
            'note' => $booking->trip->destinations->isNotEmpty() ? $booking->trip->destinationsFormatted : null,
            'strong' => true,
        ])
        @include('emails.partials.detail-row', [
            'label' => 'Vertrek',
            'value' => $booking->departure_date_formatted,
            'note' => $booking->departure_date->diffForHumans(),
        ])
        @include('emails.partials.detail-row', [
            'label' => 'Duur',
            'value' => $booking->trip->duration . ' dagen, ' . ($booking->trip->duration - 1) . ' nachten',
        ])
    </table>

    {{-- Main booker --}}
    @include('emails.partials.heading', ['text' => 'Hoofdboeker en contactpersoon'])

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
        @include('emails.partials.detail-row', ['label' => 'Naam', 'value' => $booking->mainBooker->full_name, 'strong' => true])
        @include('emails.partials.detail-row', ['label' => 'E-mail', 'value' => $booking->contact->email, 'href' => 'mailto:' . $booking->contact->email])
        @include('emails.partials.detail-row', ['label' => 'Telefoon', 'value' => $booking->contact->phone, 'href' => 'tel:' . $booking->contact->phone])
        @include('emails.partials.detail-row', ['label' => 'Geboortedatum', 'value' => $booking->mainBooker->birthdate_formatted])
        @include('emails.partials.detail-row', ['label' => 'Adres', 'value' => $booking->contact->address, 'multiline' => true])
    </table>

    {{-- Travelers --}}
    @include('emails.partials.heading', ['text' => 'Reizigers'])

    <p style="margin:-6px 0 4px 0;font-size:13px;line-height:1.5;color:#4d6f80;">
        {{ $travelerCount }} {{ $travelerCount === 1 ? 'persoon' : 'personen' }}:
        {{ $adultCount }} {{ $adultCount === 1 ? 'volwassene' : 'volwassenen' }},
        {{ $childCount }} {{ $childCount === 1 ? 'kind' : 'kinderen' }}
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
        @foreach ($booking->travelers as $index => $traveler)
            <tr>
                <td width="32" style="padding:12px 0;border-bottom:1px solid #eee8dc;vertical-align:top;font-family:Georgia,'Times New Roman',serif;font-size:15px;line-height:1.5;color:#2d5f6e;">
                    {{ $index + 1 }}
                </td>
                <td style="padding:12px 0;border-bottom:1px solid #eee8dc;vertical-align:top;font-size:15px;line-height:1.5;color:#1e2d3d;">
                    <strong style="font-weight:bold;">{{ $traveler->full_name }}</strong>
                    @if ($traveler->id === $booking->main_booker_id)
                        <span style="font-size:13px;color:#4d6f80;">&nbsp;&middot;&nbsp;hoofdboeker</span>
                    @endif
                    <br>
                    <span style="font-size:13px;color:#4d6f80;">
                        {{ $traveler->type->label() }} &middot; {{ $traveler->birthdate_formatted }} &middot; {{ $traveler->nationality }}
                    </span>
                </td>
            </tr>
        @endforeach
    </table>

    {{-- Status --}}
    @include('emails.partials.heading', ['text' => 'Status'])

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
        @include('emails.partials.detail-row', [
            'label' => 'Voorwaarden geaccepteerd',
            'value' => $booking->has_accepted_conditions ? 'Ja' : 'Nee',
            'tone' => $booking->has_accepted_conditions ? 'success' : 'danger',
        ])
        @include('emails.partials.detail-row', [
            'label' => 'Bevestigd',
            'value' => $booking->has_confirmed ? 'Ja' : 'Wacht op bevestiging',
            'tone' => $booking->has_confirmed ? 'success' : 'warning',
        ])
        @if ($statusLabel)
            @include('emails.partials.detail-row', ['label' => 'Boekingsstatus', 'value' => $statusLabel])
        @endif
    </table>

    {{-- Next steps --}}
    @include('emails.partials.heading', ['text' => 'Wat nu?'])

    @include('emails.partials.steps', ['steps' => [
        ['Controleer de boekingsgegevens', 'Kijk of alles volledig en juist is.'],
        ['Neem binnen 2 werkdagen contact op', 'Bel de klant voor de definitieve bevestiging.'],
        ['Markeer de boeking als bevestigd', 'Doe dit in het admin panel na het telefonisch contact.'],
        ['Verstuur de reisdocumenten', 'Uiterlijk 2 weken voor vertrek.'],
    ]])

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center" style="padding:20px 0 0 0;">
                @include('emails.partials.button', ['url' => $adminUrl, 'text' => 'Boeking openen'])
            </td>
        </tr>
    </table>
@endsection

@section('footer')
    <p style="margin:0;font-size:12px;line-height:1.7;color:#4d6f80;">
        Automatische notificatie van een nieuwe boeking op {{ config('app.name') }}.
    </p>
@endsection
