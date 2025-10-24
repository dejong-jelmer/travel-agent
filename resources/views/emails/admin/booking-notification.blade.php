@extends('emails.default')

@section('content')
    {{-- Alert Header --}}
    <div style="background:#B17C65;color:#FFFFFF;padding:24px;border-radius:8px;margin-bottom:30px;text-align:center;">
        <h2 style="margin:0 0 8px 0;font-size:26px;font-weight:700;">🔔 Nieuwe Boeking Ontvangen</h2>
        <p style="margin:0;font-size:15px;opacity:0.95;">
            Er is zojuist een nieuwe boeking binnengekomen die uw aandacht vereist
        </p>
    </div>

    {{-- Quick Action Button --}}
    <div style="margin:0 0 35px 0;text-align:center;">
        <a href="{{ route('admin.bookings.edit', $booking) }}"
            style="display:inline-block;background:#2F3E46;color:#FFFFFF;padding:16px 32px;text-decoration:none;border-radius:8px;font-size:16px;font-weight:600;box-shadow:0 2px 4px rgba(0,0,0,0.1);">
            📋 Bekijk Boeking in Admin Panel
        </a>
    </div>

    {{-- Booking Reference --}}
    <div style="background:#1B3A4B;color:#FFFFFF;padding:20px;border-radius:8px;margin-bottom:30px;text-align:center;">
        <p style="margin:0 0 8px 0;font-size:13px;text-transform:uppercase;opacity:0.8;letter-spacing:0.5px;">
            Boekingsnummer
        </p>
        <p style="margin:0;font-size:32px;font-weight:700;letter-spacing:2px;">
            {{ $booking->reference }}
        </p>
        <p style="margin:12px 0 0 0;font-size:13px;opacity:0.8;">
            Aangemaakt: {{ $booking->created_at->setTimezone('Europe/Amsterdam')->format('d-m-Y \o\m H:i') }} uur
        </p>
    </div>

    {{-- Trip & Timing Details --}}
    <h3 style="color:#1B3A4B;font-size:20px;margin:35px 0 15px 0;padding-bottom:12px;border-bottom:3px solid #AFCB98;font-weight:700;">
        🗺️ Reis Details
    </h3>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:25px;">
        <tr>
            <td style="background:#F2F4F3;border-radius:8px;padding:20px;border-left:4px solid #AFCB98;">
                <p style="margin:0 0 8px 0;font-size:13px;color:#6B8E7A;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">
                    Gekozen Reis
                </p>
                <p style="margin:0 0 15px 0;font-size:22px;font-weight:700;color:#1B3A4B;">
                    {{ $booking->product->name }}
                </p>
                @if($booking->product->countries->isNotEmpty())
                <p style="margin:0;font-size:14px;color:#6B8E7A;">
                    📍 {{ $booking->product->countries->pluck('name')->join(', ') }}
                </p>
                @endif
            </td>
        </tr>
    </table>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:30px;">
        <tr>
            <td width="48%" style="padding-right:10px;">
                <div style="background:#F2F4F3;border-radius:8px;padding:18px;height:100%;border-left:4px solid #B17C65;">
                    <p style="margin:0 0 8px 0;font-size:12px;color:#6B8E7A;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">
                        📅 Vertrekdatum
                    </p>
                    <p style="margin:0;font-size:18px;font-weight:700;color:#1B3A4B;">
                        {{ $booking->departure_date_formatted }}
                    </p>
                    <p style="margin:5px 0 0 0;font-size:13px;color:#6B8E7A;">
                        {{ $booking->departure_date->diffForHumans() }}
                    </p>
                </div>
            </td>
            <td width="4%"></td>
            <td width="48%" style="padding-left:10px;">
                <div style="background:#F2F4F3;border-radius:8px;padding:18px;height:100%;border-left:4px solid #B17C65;">
                    <p style="margin:0 0 8px 0;font-size:12px;color:#6B8E7A;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">
                        ⏱️ Duur
                    </p>
                    <p style="margin:0;font-size:18px;font-weight:700;color:#1B3A4B;">
                        {{ $booking->product->duration }} dagen
                    </p>
                    <p style="margin:5px 0 0 0;font-size:13px;color:#6B8E7A;">
                        {{ $booking->product->duration - 1 }} nachten
                    </p>
                </div>
            </td>
        </tr>
    </table>

    {{-- Main Booker (Contact Person) --}}
    <h3 style="color:#1B3A4B;font-size:20px;margin:35px 0 15px 0;padding-bottom:12px;border-bottom:3px solid #AFCB98;font-weight:700;">
        👤 Hoofdboeker & Contactpersoon
    </h3>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:30px;background:#FFF8F3;border-radius:8px;padding:20px;border:2px solid #B17C65;">
        <tr>
            <td width="50%" style="padding-right:15px;border-right:2px solid #F2F4F3;">
                <p style="margin:0 0 6px 0;font-size:12px;color:#B17C65;font-weight:700;text-transform:uppercase;">
                    Naam
                </p>
                <p style="margin:0 0 18px 0;font-size:18px;font-weight:700;color:#1B3A4B;">
                    {{ $booking->mainBooker->full_name }}
                </p>

                <p style="margin:0 0 6px 0;font-size:12px;color:#B17C65;font-weight:700;text-transform:uppercase;">
                    Geboortedatum
                </p>
                <p style="margin:0;font-size:15px;color:#1B3A4B;">
                    {{ $booking->mainBooker->birthdate_formatted }}
                </p>
            </td>
            <td width="50%" style="padding-left:15px;">
                <p style="margin:0 0 6px 0;font-size:12px;color:#B17C65;font-weight:700;text-transform:uppercase;">
                    📧 E-mail
                </p>
                <p style="margin:0 0 18px 0;font-size:15px;color:#1B3A4B;">
                    <a href="mailto:{{ $booking->contact->email }}" style="color:#2F3E46;text-decoration:none;font-weight:600;">
                        {{ $booking->contact->email }}
                    </a>
                </p>

                <p style="margin:0 0 6px 0;font-size:12px;color:#B17C65;font-weight:700;text-transform:uppercase;">
                    📞 Telefoon
                </p>
                <p style="margin:0;font-size:15px;color:#1B3A4B;">
                    <a href="tel:{{ $booking->contact->phone }}" style="color:#2F3E46;text-decoration:none;font-weight:600;">
                        {{ $booking->contact->phone }}
                    </a>
                </p>
            </td>
        </tr>
    </table>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:30px;background:#F2F4F3;border-radius:8px;padding:20px;">
        <tr>
            <td>
                <p style="margin:0 0 6px 0;font-size:12px;color:#6B8E7A;font-weight:700;text-transform:uppercase;">
                    📍 Adres
                </p>
                <p style="margin:0;font-size:15px;color:#1B3A4B;white-space:pre-line;line-height:1.6;">{{ $booking->contact->address }}</p>
            </td>
        </tr>
    </table>

    {{-- All Travelers --}}
    <h3 style="color:#1B3A4B;font-size:20px;margin:35px 0 15px 0;padding-bottom:12px;border-bottom:3px solid #AFCB98;font-weight:700;">
        👥 Alle Reizigers ({{ $booking->travelers->count() }} {{ $booking->travelers->count() === 1 ? 'persoon' : 'personen' }})
    </h3>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:30px;">
        @foreach($booking->travelers as $index => $traveler)
        <tr>
            <td style="padding-bottom:12px;">
                <table width="100%" cellpadding="0" cellspacing="0"
                    style="background:{{ $traveler->id === $booking->main_booker_id ? '#FFF8F3' : '#F2F4F3' }};
                           border-radius:8px;padding:16px;
                           border-left:4px solid {{ $traveler->id === $booking->main_booker_id ? '#B17C65' : '#AFCB98' }};">
                    <tr>
                        <td width="45">
                            <div style="width:38px;height:38px;background:{{ $traveler->id === $booking->main_booker_id ? '#B17C65' : '#AFCB98' }};border-radius:50%;display:flex;align-items:center;justify-content:center;color:#FFFFFF;font-weight:700;font-size:16px;text-align:center;line-height:38px;">
                                {{ $index + 1 }}
                            </div>
                        </td>
                        <td>
                            <p style="margin:0 0 2px 0;font-size:16px;font-weight:700;color:#1B3A4B;">
                                {{ $traveler->full_name }}
                                @if($traveler->id === $booking->main_booker_id)
                                    <span style="background:#B17C65;color:#FFFFFF;font-size:10px;padding:4px 10px;border-radius:12px;margin-left:8px;font-weight:700;text-transform:uppercase;">
                                        Hoofdboeker
                                    </span>
                                @endif
                            </p>
                            <p style="margin:0;font-size:13px;color:#6B8E7A;">
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
                <div style="background:#E8F5E9;border-radius:8px;padding:16px;text-align:center;">
                    <p style="margin:0 0 5px 0;font-size:13px;color:#6B8E7A;font-weight:700;text-transform:uppercase;">
                        👨‍👩‍👧‍👦 Volwassenen
                    </p>
                    <p style="margin:0;font-size:28px;font-weight:700;color:#1B3A4B;">
                        {{ $booking->adults->count() }}
                    </p>
                </div>
            </td>
            <td width="4%"></td>
            <td width="48%" style="padding-left:10px;">
                <div style="background:#FFF8F3;border-radius:8px;padding:16px;text-align:center;">
                    <p style="margin:0 0 5px 0;font-size:13px;color:#B17C65;font-weight:700;text-transform:uppercase;">
                        👶 Kinderen
                    </p>
                    <p style="margin:0;font-size:28px;font-weight:700;color:#1B3A4B;">
                        {{ $booking->children->count() }}
                    </p>
                </div>
            </td>
        </tr>
    </table>

    {{-- Additional Info --}}
    <h3 style="color:#1B3A4B;font-size:20px;margin:35px 0 15px 0;padding-bottom:12px;border-bottom:3px solid #AFCB98;font-weight:700;">
        ℹ️ Aanvullende Informatie
    </h3>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:30px;background:#F2F4F3;border-radius:8px;padding:20px;">
        <tr>
            <td>
                <table width="100%" cellpadding="8" cellspacing="0">
                    <tr>
                        <td width="50%" style="font-size:14px;color:#6B8E7A;">
                            <strong style="color:#1B3A4B;">Voorwaarden geaccepteerd:</strong>
                        </td>
                        <td width="50%" style="font-size:14px;">
                            @if($booking->conditions_accepted)
                                <span style="background:#AFCB98;color:#FFFFFF;padding:4px 12px;border-radius:12px;font-weight:700;font-size:12px;">
                                    ✓ JA
                                </span>
                            @else
                                <span style="background:#DC3545;color:#FFFFFF;padding:4px 12px;border-radius:12px;font-weight:700;font-size:12px;">
                                    ✗ NEE
                                </span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size:14px;color:#6B8E7A;">
                            <strong style="color:#1B3A4B;">Bevestigd door admin:</strong>
                        </td>
                        <td style="font-size:14px;">
                            @if($booking->is_confirmed)
                                <span style="background:#AFCB98;color:#FFFFFF;padding:4px 12px;border-radius:12px;font-weight:700;font-size:12px;">
                                    ✓ JA
                                </span>
                            @else
                                <span style="background:#FFA500;color:#FFFFFF;padding:4px 12px;border-radius:12px;font-weight:700;font-size:12px;">
                                    ⏳ WACHT OP BEVESTIGING
                                </span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size:14px;color:#6B8E7A;">
                            <strong style="color:#1B3A4B;">Status:</strong>
                        </td>
                        <td style="font-size:14px;">
                            @if($booking->new)
                                <span style="background:#B17C65;color:#FFFFFF;padding:4px 12px;border-radius:12px;font-weight:700;font-size:12px;">
                                    🆕 NIEUW
                                </span>
                            @else
                                <span style="background:#6B8E7A;color:#FFFFFF;padding:4px 12px;border-radius:12px;font-weight:700;font-size:12px;">
                                    VERWERKT
                                </span>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Action Required Box --}}
    <div style="background:#FFF3CD;border-left:4px solid #FFA500;padding:20px;border-radius:8px;margin:30px 0;">
        <h3 style="color:#1B3A4B;font-size:18px;margin:0 0 12px 0;font-weight:700;">
            ⚠️ Actie Vereist
        </h3>
        <ul style="margin:0;padding:0 0 0 20px;color:#1B3A4B;line-height:1.8;">
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
            style="display:inline-block;background:#2F3E46;color:#FFFFFF;padding:18px 40px;text-decoration:none;border-radius:8px;font-size:17px;font-weight:700;box-shadow:0 4px 6px rgba(0,0,0,0.15);">
            📋 Ga naar Boeking in Admin Panel →
        </a>
    </div>

    {{-- Footer Note --}}
    <p style="margin:30px 0 0 0;padding:20px;background:#F8F9FA;border-radius:8px;font-size:13px;color:#6B8E7A;text-align:center;line-height:1.6;">
        Dit is een automatische notificatie. Zorg ervoor dat u tijdig actie onderneemt om de klant een uitstekende service te bieden.
    </p>
@endsection
