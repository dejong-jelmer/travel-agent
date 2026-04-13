@extends('emails.default')

@section('content')
    {{-- Alert Header --}}
    <div style="background:#ffffff;border-left:4px solid #f59e0b;padding:20px;margin:30px 0;">
        <p style="margin:0;font-size:15px;color:#1e2d3d;line-height:1.8;">
            Nieuwe Boeking Ontvangen:
        </p>
        <p style="margin:0;font-size:15px;color:#1e2d3d;line-height:1.8;">
            Er is zojuist een nieuwe boeking binnengekomen die uw aandacht vereist
        </p>
    </div>

    {{-- Booking Reference --}}
    <div style="background:#fbfbf7;padding:16px;border-radius:8px;margin-bottom:30px;text-align:center;">
        <p style="margin:0 0 4px 0;font-size:13px;color:#82b2ca;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">
            Boekingsnummer
        </p>
        <p style="margin:0;font-size:32px;font-weight:700;color:#1e2d3d;letter-spacing:2px;">
            {{ $booking->reference }}
        </p>
        <p style="margin:12px 0 0 0;font-size:13px;color:#82b2ca;font-weight:600;">
            Aangemaakt: {{ $booking->created_at_formatted }} uur
        </p>
        <a href="{{ route('admin.bookings.edit', $booking) }}" style="color:#82b2ca;margin:12px 0 0 0;display:inline-block;font-size:13px;">
            Bekijk Boeking in Admin Panel
        </a>
    </div>

    {{-- Trip & Timing Details --}}
    <h3 style="color:#1e2d3d;font-size:20px;margin:35px 0 15px 0;padding-bottom:12px;">
        Reis Details
    </h3>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:30px;background:#FFFFFF;padding:10px;">
        <tr>
            <td>
                <table width="100%" cellpadding="12" cellspacing="0">
                    <tr>
                        <td width="30%" style="vertical-align:top;">
                            <p style="margin:0;font-size:12px;color:#1e2d3d;font-weight:700;letter-spacing:0.5px;">
                                Gekozen Reis:
                            </p>
                        </td>
                        <td width="70%" style="vertical-align:top;">
                            <p style="margin:0;font-size:16px;font-weight:700;color:#1e2d3d;">
                                {{ $booking->trip->name }}
                            </p>
                            @if ($booking->trip->destinations->isNotEmpty())
                                <p style="margin:4px 0 0 0;font-size:14px;color:#82b2ca;">
                                    {{ $booking->trip->destinationsFormatted }}
                                </p>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top;padding-top:8px;">
                            <p style="margin:0;font-size:12px;color:#1e2d3d;font-weight:700;letter-spacing:0.5px;">
                                Vertrekdatum:
                            </p>
                        </td>
                        <td style="vertical-align:top;padding-top:8px;">
                            <p style="margin:0;font-size:15px;font-weight:600;color:#1e2d3d;">
                                {{ $booking->departure_date_formatted }}
                            </p>
                            <p style="margin:2px 0 0 0;font-size:13px;color:#82b2ca;">
                                {{ $booking->departure_date->diffForHumans() }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top;padding-top:8px;">
                            <p style="margin:0;font-size:12px;color:#1e2d3d;font-weight:700;letter-spacing:0.5px;">
                                Duur:
                            </p>
                        </td>
                        <td style="vertical-align:top;padding-top:8px;">
                            <p style="margin:0;font-size:15px;color:#1e2d3d;">
                                {{ $booking->trip->duration }} dagen / {{ $booking->trip->duration - 1 }} nachten
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Main Booker (Contact Person) --}}
    <h3 style="color:#1e2d3d;font-size:20px;margin:35px 0 15px 0;padding-bottom:12px;">
        Hoofdboeker & Contactpersoon
    </h3>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:30px;background:#FFFFFF;padding:10px;">
        <tr>
            <td>
                <table width="100%" cellpadding="12" cellspacing="0">
                    <tr>
                        <td width="30%" style="vertical-align:top;">
                            <p style="margin:0;font-size:12px;color:#1e2d3d;font-weight:700;letter-spacing:0.5px;">
                                Naam:
                            </p>
                        </td>
                        <td width="70%" style="vertical-align:top;">
                            <p style="margin:0;font-size:16px;font-weight:700;color:#1e2d3d;">
                                {{ $booking->mainBooker->full_name }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top;padding-top:8px;">
                            <p style="margin:0;font-size:12px;color:#1e2d3d;font-weight:700;letter-spacing:0.5px;">
                                E-mail:
                            </p>
                        </td>
                        <td style="vertical-align:top;padding-top:8px;">
                            <p style="margin:0;font-size:15px;color:#30547e;">
                                <a href="mailto:{{ $booking->contact->email }}" style="color:#1e2d3d;text-decoration:none;font-weight:600;">
                                    {{ $booking->contact->email }}
                                </a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top;padding-top:8px;">
                            <p style="margin:0;font-size:12px;color:#1e2d3d;font-weight:700;letter-spacing:0.5px;">
                                Telefoon:
                            </p>
                        </td>
                        <td style="vertical-align:top;padding-top:8px;">
                            <p style="margin:0;font-size:15px;color:#1e2d3d;">
                                <a href="tel:{{ $booking->contact->phone }}" style="color:#1e2d3d;text-decoration:none;font-weight:600;">
                                    {{ $booking->contact->phone }}
                                </a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top;padding-top:8px;">
                            <p style="margin:0;font-size:12px;color:#1e2d3d;font-weight:700;letter-spacing:0.5px;">
                                Geboortedatum:
                            </p>
                        </td>
                        <td style="vertical-align:top;padding-top:8px;">
                            <p style="margin:0;font-size:15px;color:#1e2d3d;">
                                {{ $booking->mainBooker->birthdate_formatted }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top;padding-top:8px;">
                            <p style="margin:0;font-size:12px;color:#1e2d3d;font-weight:700;letter-spacing:0.5px;">
                                Adres:
                            </p>
                        </td>
                        <td style="vertical-align:top;padding-top:8px;">
                            <p style="margin:0;font-size:15px;color:#1e2d3d;white-space:pre-line;line-height:1.6;">{{ $booking->contact->address }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- All Travelers --}}
    <h3 style="color:#1e2d3d;font-size:20px;margin:35px 0 15px 0;padding-bottom:12px;">
        Alle Reizigers ({{ $booking->travelers->count() }}
        {{ $booking->travelers->count() === 1 ? 'persoon' : 'personen' }})
    </h3>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:30px;">
        @foreach ($booking->travelers as $index => $traveler)
            <tr>
                <td style="padding-bottom:12px;">
                    <table width="100%" cellpadding="0" cellspacing="0"
                        style="background:#ffffff;padding:16px;border-left:4px solid {{ $traveler->id === $booking->main_booker_id ? '#f59e0b' : '#afcb98' }};">
                        <tr>
                            <td width="45">
                                <div
                                    style="width:38px;height:38px;background:{{ $traveler->id === $booking->main_booker_id ? '#f59e0b' : '#afcb98' }};border-radius:50%;color:#FFFFFF;font-weight:700;font-size:16px;text-align:center;line-height:38px;">
                                    {{ $index + 1 }}
                                </div>
                            </td>
                            <td>
                                <p style="margin:0 0 2px 0;font-size:16px;font-weight:700;color:#1e2d3d;">
                                    {{ $traveler->full_name }}
                                    @if ($traveler->id === $booking->main_booker_id)
                                        <span
                                            style="background:#f59e0b;color:#FFFFFF;font-size:10px;padding:4px 10px;border-radius:12px;margin-left:8px;font-weight:700;text-transform:uppercase;">
                                            Hoofdboeker
                                        </span>
                                    @endif
                                </p>
                                <p style="margin:0;font-size:13px;color:#82b2ca;">
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
    <div style="background:#fbfbf7;padding:16px;border-radius:8px;margin-bottom:30px;text-align:center;">
        <p style="margin:0;font-size:13px;color:#82b2ca;font-weight:600;">
            {{ $booking->adults->count() }} volwassene(n) · {{ $booking->children->count() }} kind(eren)
        </p>
    </div>

    {{-- Additional Info --}}
    <h3 style="color:#1e2d3d;font-size:20px;margin:35px 0 15px 0;padding-bottom:12px;">
        Aanvullende Informatie
    </h3>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:30px;background:#FFFFFF;padding:10px;">
        <tr>
            <td>
                <table width="100%" cellpadding="12" cellspacing="0">
                    <tr>
                        <td width="30%" style="vertical-align:top;">
                            <p style="margin:0;font-size:12px;color:#1e2d3d;font-weight:700;letter-spacing:0.5px;">
                                Voorwaarden:
                            </p>
                        </td>
                        <td width="70%" style="vertical-align:top;">
                            @if ($booking->has_accepted_conditions)
                                <span style="background:#afcb98;color:#FFFFFF;padding:4px 12px;border-radius:12px;font-weight:700;font-size:12px;">
                                    JA
                                </span>
                            @else
                                <span style="background:#dc3545;color:#FFFFFF;padding:4px 12px;border-radius:12px;font-weight:700;font-size:12px;">
                                    NEE
                                </span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top;padding-top:8px;">
                            <p style="margin:0;font-size:12px;color:#1e2d3d;font-weight:700;letter-spacing:0.5px;">
                                Bevestigd:
                            </p>
                        </td>
                        <td style="vertical-align:top;padding-top:8px;">
                            @if ($booking->has_confirmed)
                                <span style="background:#afcb98;color:#FFFFFF;padding:4px 12px;border-radius:12px;font-weight:700;font-size:12px;">
                                    JA
                                </span>
                            @else
                                <span style="background:#f59e0b;color:#FFFFFF;padding:4px 12px;border-radius:12px;font-weight:700;font-size:12px;">
                                    WACHT OP BEVESTIGING
                                </span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top;padding-top:8px;">
                            <p style="margin:0;font-size:12px;color:#1e2d3d;font-weight:700;letter-spacing:0.5px;">
                                Status:
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Action Required Box --}}
    <div style="background:#ffffff;border-left:4px solid #f59e0b;padding:20px;margin:30px 0;">
        <p style="margin:0 0 12px 0;font-size:15px;color:#1e2d3d;font-weight:700;">
            Actie Vereist
        </p>
        <ul style="margin:0;padding:0 0 0 20px;color:#1e2d3d;line-height:1.8;">
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

    {{-- Quick Action Button --}}
    <div style="margin:30px 0;text-align:center;">
        <a href="{{ route('admin.bookings.edit', $booking) }}"
            style="display:inline-block;background:#2d5f6e;color:#FFFFFF;padding:16px 32px;text-decoration:none;border-radius:8px;font-size:16px;font-weight:600;box-shadow:0 2px 4px rgba(0,0,0,0.1);">
            Ga naar Boeking in Admin Panel
        </a>
    </div>

    {{-- Footer Note --}}
    <p style="margin:30px 0 0 0;padding:20px;background:#fbfbf7;border-radius:8px;font-size:13px;color:#82b2ca;text-align:center;line-height:1.6;">
        Dit is een automatische notificatie. Zorg ervoor dat u tijdig actie onderneemt om de klant een uitstekende service
        te bieden.
    </p>
@endsection
