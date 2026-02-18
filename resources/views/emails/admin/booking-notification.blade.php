@extends('emails.default')

@section('content')
    {{-- Alert Header --}}
    <div style="background:#fbfbf7;border-left:4px solid #f0972d;padding:20px;margin:30px 0;">
        <h2 style="margin:0 0 8px 0;font-size:26px;font-weight:700;">Nieuwe Boeking Ontvangen</h2>
        <p style="margin:0;font-size:15px;opacity:0.95;">
            Er is zojuist een nieuwe boeking binnengekomen die uw aandacht vereist
        </p>
    </div>

    {{-- Booking Reference --}}
    <div style="background:#fbfbf7;border-left:4px solid #2F3E46;padding:20px;margin:30px 0;">
        <p style="margin:0 0 8px 0;font-size:13px;text-transform:uppercase;opacity:0.8;letter-spacing:0.5px;">
            Boekingsnummer
        </p>
        <p style="margin:0;font-size:32px;font-weight:700;letter-spacing:2px;">
            {{ $booking->reference }}
        </p>
        <p style="margin:12px 0 0 0;font-size:13px;opacity:0.8;">
            Aangemaakt: {{ $booking->created_at_formatted }} uur
        </p>
        <a href="{{ route('admin.bookings.edit', $booking) }}" style="color:#82b2ca; margin:20px 0 0 0; display:block;">
            Bekijk Boeking in Admin Panel
        </a>
    </div>

    {{-- Trip & Timing Details --}}
    <h3
        style="color:#2F3E46;font-size:20px;margin:35px 0 15px 0;padding-bottom:12px;border-bottom:3px solid #AFCB98;font-weight:700;">
        Reis Details
    </h3>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:25px;">
        <tr>
            <td style="background:#fbfbf7;padding:20px;border-left:4px solid #AFCB98;">
                <p
                    style="margin:0 0 8px 0;font-size:13px;color:#AFCB98;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">
                    Gekozen Reis
                </p>
                <p style="margin:0 0 15px 0;font-size:22px;font-weight:700;color:#2F3E46;">
                    {{ $booking->trip->name }}
                </p>
                @if ($booking->trip->destinations->isNotEmpty())
                    <p style="margin:0;font-size:14px;color:#A3BCCB;">
                        {{ $booking->trip->destinationsFormatted }}
                    </p>
                @endif
            </td>
        </tr>
    </table>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:30px;">
        <tr>
            <td width="48%" style="padding-right:10px;">
                <div style="background:#fbfbf7;padding:18px;height:100%;border-left:4px solid #f0972d;">
                    <p
                        style="margin:0 0 8px 0;font-size:12px;color:#A3BCCB;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">
                        Vertrekdatum
                    </p>
                    <p style="margin:0;font-size:18px;font-weight:700;color:#2F3E46;">
                        {{ $booking->departure_date_formatted }}
                    </p>
                    <p style="margin:5px 0 0 0;font-size:13px;color:#A3BCCB;">
                        {{ $booking->departure_date->diffForHumans() }}
                    </p>
                </div>
            </td>
            <td width="4%"></td>
            <td width="48%" style="padding-left:10px;">
                <div style="background:#fbfbf7;padding:18px;height:100%;border-left:4px solid #f0972d;">
                    <p
                        style="margin:0 0 8px 0;font-size:12px;color:#A3BCCB;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">
                        Duur
                    </p>
                    <p style="margin:0;font-size:18px;font-weight:700;color:#2F3E46;">
                        {{ $booking->trip->duration }} dagen
                    </p>
                    <p style="margin:5px 0 0 0;font-size:13px;color:#A3BCCB;">
                        {{ $booking->trip->duration - 1 }} nachten
                    </p>
                </div>
            </td>
        </tr>
    </table>

    {{-- Main Booker (Contact Person) --}}
    <h3
        style="color:#2F3E46;font-size:20px;margin:35px 0 15px 0;padding-bottom:12px;border-bottom:3px solid #AFCB98;font-weight:700;">
        Hoofdboeker & Contactpersoon
    </h3>

    <table width="100%" cellpadding="0" cellspacing="0"
        style="margin-bottom:30px;background:#FFF8F3;padding:20px;border:2px solid #f0972d;">
        <tr>
            <td width="50%" style="padding-right:15px;border-right:2px solid #fbfbf7;">
                <p style="margin:0 0 6px 0;font-size:12px;color:#f0972d;font-weight:700;text-transform:uppercase;">
                    Naam
                </p>
                <p style="margin:0 0 18px 0;font-size:18px;font-weight:700;color:#2F3E46;">
                    {{ $booking->mainBooker->full_name }}
                </p>

                <p style="margin:0 0 6px 0;font-size:12px;color:#f0972d;font-weight:700;text-transform:uppercase;">
                    Geboortedatum
                </p>
                <p style="margin:0;font-size:15px;color:#2F3E46;">
                    {{ $booking->mainBooker->birthdate_formatted }}
                </p>
            </td>
            <td width="50%" style="padding-left:15px;">
                <p style="margin:0 0 6px 0;font-size:12px;color:#f0972d;font-weight:700;text-transform:uppercase;">
                    E-mail
                </p>
                <p style="margin:0 0 18px 0;font-size:15px;color:#2F3E46;">
                    <a href="mailto:{{ $booking->contact->email }}"
                        style="color:#2F3E46;text-decoration:none;font-weight:600;">
                        {{ $booking->contact->email }}
                    </a>
                </p>

                <p style="margin:0 0 6px 0;font-size:12px;color:#f0972d;font-weight:700;text-transform:uppercase;">
                    Telefoon
                </p>
                <p style="margin:0;font-size:15px;color:#2F3E46;">
                    <a href="tel:{{ $booking->contact->phone }}"
                        style="color:#2F3E46;text-decoration:none;font-weight:600;">
                        {{ $booking->contact->phone }}
                    </a>
                </p>
            </td>
        </tr>
    </table>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:30px;background:#fbfbf7;padding:20px;">
        <tr>
            <td>
                <p style="margin:0 0 6px 0;font-size:12px;color:#A3BCCB;font-weight:700;text-transform:uppercase;">
                    Adres
                </p>
                <p style="margin:0;font-size:15px;color:#2F3E46;white-space:pre-line;line-height:1.6;">
                    {{ $booking->contact->address }}</p>
            </td>
        </tr>
    </table>

    {{-- All Travelers --}}
    <h3
        style="color:#2F3E46;font-size:20px;margin:35px 0 15px 0;padding-bottom:12px;border-bottom:3px solid #AFCB98;font-weight:700;">
        Alle Reizigers ({{ $booking->travelers->count() }}
        {{ $booking->travelers->count() === 1 ? 'persoon' : 'personen' }})
    </h3>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:30px;">
        @foreach ($booking->travelers as $index => $traveler)
            <tr>
                <td style="padding-bottom:12px;">
                    <table width="100%" cellpadding="0" cellspacing="0"
                        style="background:{{ $traveler->id === $booking->main_booker_id ? '#FFF8F3' : '#fbfbf7' }};
                           padding:16px;
                           border-left:4px solid {{ $traveler->id === $booking->main_booker_id ? '#f0972d' : '#AFCB98' }};">
                        <tr>
                            <td width="45">
                                <div
                                    style="width:38px;height:38px;background:{{ $traveler->id === $booking->main_booker_id ? '#f0972d' : '#AFCB98' }};border-radius:50%;display:flex;align-items:center;justify-content:center;color:#FFFFFF;font-weight:700;font-size:16px;text-align:center;line-height:38px;">
                                    {{ $index + 1 }}
                                </div>
                            </td>
                            <td>
                                <p style="margin:0 0 2px 0;font-size:16px;font-weight:700;color:#2F3E46;">
                                    {{ $traveler->full_name }}
                                    @if ($traveler->id === $booking->main_booker_id)
                                        <span
                                            style="background:#f0972d;color:#FFFFFF;font-size:10px;padding:4px 10px;border-radius:12px;margin-left:8px;font-weight:700;text-transform:uppercase;">
                                            Hoofdboeker
                                        </span>
                                    @endif
                                </p>
                                <p style="margin:0;font-size:13px;color:#A3BCCB;">
                                    <strong>Type:</strong> {{ $traveler->type->label() }} |
                                    <strong>Geboortedatum:</strong> {{ $traveler->birthdate_formatted }} |
                                    <strong>Nationaliteit:</strong> {{ $traveler->nationality }}
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        @endforeach
    </table>

    {{-- Travelers Summary --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:35px;">
        <tr>
            <td width="48%" style="padding-right:10px;">
                <div style="background:#fbfbf7;padding:16px;text-align:center;border:2px solid #AFCB98;">
                    <p style="margin:0 0 5px 0;font-size:13px;color:#AFCB98;font-weight:700;text-transform:uppercase;">
                        Volwassenen
                    </p>
                    <p style="margin:0;font-size:28px;font-weight:700;color:#2F3E46;">
                        {{ $booking->adults->count() }}
                    </p>
                </div>
            </td>
            <td width="4%"></td>
            <td width="48%" style="padding-left:10px;">
                <div style="background:#FFF8F3;padding:16px;text-align:center;border:2px solid #f0972d;">
                    <p style="margin:0 0 5px 0;font-size:13px;color:#f0972d;font-weight:700;text-transform:uppercase;">
                        Kinderen
                    </p>
                    <p style="margin:0;font-size:28px;font-weight:700;color:#2F3E46;">
                        {{ $booking->children->count() }}
                    </p>
                </div>
            </td>
        </tr>
    </table>

    {{-- Additional Info --}}
    <h3
        style="color:#2F3E46;font-size:20px;margin:35px 0 15px 0;padding-bottom:12px;border-bottom:3px solid #AFCB98;font-weight:700;">
        Aanvullende Informatie
    </h3>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:30px;background:#fbfbf7;padding:20px;">
        <tr>
            <td>
                <table width="100%" cellpadding="8" cellspacing="0">
                    <tr>
                        <td width="50%" style="font-size:14px;color:#A3BCCB;">
                            <strong style="color:#2F3E46;">Voorwaarden geaccepteerd:</strong>
                        </td>
                        <td width="50%" style="font-size:14px;">
                            @if ($booking->has_accepted_conditions)
                                <span
                                    style="background:#AFCB98;color:#FFFFFF;padding:4px 12px;border-radius:12px;font-weight:700;font-size:12px;">
                                    JA
                                </span>
                            @else
                                <span
                                    style="background:#e63946;color:#FFFFFF;padding:4px 12px;border-radius:12px;font-weight:700;font-size:12px;">
                                    NEE
                                </span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size:14px;color:#A3BCCB;">
                            <strong style="color:#2F3E46;">Bevestigd door admin:</strong>
                        </td>
                        <td style="font-size:14px;">
                            @if ($booking->has_confirmed)
                                <span
                                    style="background:#AFCB98;color:#FFFFFF;padding:4px 12px;border-radius:12px;font-weight:700;font-size:12px;">
                                    JA
                                </span>
                            @else
                                <span
                                    style="background:#f4a261;color:#FFFFFF;padding:4px 12px;border-radius:12px;font-weight:700;font-size:12px;">
                                    WACHT OP BEVESTIGING
                                </span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size:14px;color:#A3BCCB;">
                            <strong style="color:#2F3E46;">Status:</strong>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Action Required Box --}}
    <div style="background:#fbfbf7;border-left:4px solid #f4a261;padding:20px;margin:30px 0;">
        <h3 style="color:#2F3E46;font-size:18px;margin:0 0 12px 0;font-weight:700;">
            Actie Vereist
        </h3>
        <ul style="margin:0;padding:0 0 0 20px;color:#2F3E46;line-height:1.8;">
            <li style="margin-bottom:8px;">
                <strong>Controleer de boekingsgegevens</strong> op volledigheid en juistheid
            </li>
            <li style="margin-bottom:8px;">
                <strong>Neem binnen 2 werkdagen contact op</strong> met de klant voor bevestiging
            </li>
            <li style="margin-bottom:8px;">
                <strong>Markeer de boeking als bevestigd</strong> in het admin panel na telefonisch contact
            </li>
            <li style="margin-bottom:0;">
                <strong>Verstuur reisdocumenten</strong> uiterlijk 2 weken voor vertrek
            </li>
        </ul>
    </div>

    {{-- Quick Action Button (repeated) --}}
    <div style="margin:35px 0;text-align:center;">
        <a href="{{ route('admin.bookings.edit', $booking) }}"
            style="display:inline-block;background:#2F3E46;color:#FFFFFF;padding:18px 40px;text-decoration:none;font-size:17px;font-weight:700;box-shadow:0 4px 6px rgba(0,0,0,0.15);">
            Ga naar Boeking in Admin Panel →
        </a>
    </div>

    {{-- Footer Note --}}
    <p
        style="margin:30px 0 0 0;padding:20px;background:#fbfbf7;font-size:13px;color:#A3BCCB;text-align:center;line-height:1.6;">
        Dit is een automatische notificatie. Zorg ervoor dat u tijdig actie onderneemt om de klant een uitstekende service
        te bieden.
    </p>
@endsection
