@extends('emails.default')

@section('content')
    {{-- Greeting --}}
    <h2 style="color:#1B3A4B;font-size:24px;margin:0 0 20px 0;font-weight:600;">
        Beste {{ $booking->mainBooker->first_name }},
    </h2>

    {{-- Success Message --}}
    <div style="background:#AFCB98;color:#FFFFFF;padding:20px;border-radius:8px;margin-bottom:30px;text-align:center;">
        <h3 style="margin:0 0 10px 0;font-size:20px;font-weight:600;">✓ Uw boeking is succesvol ontvangen!</h3>
        <p style="margin:0;font-size:14px;opacity:0.95;">
            Bedankt voor uw vertrouwen in {{ config('app.name') }}
        </p>
    </div>

    {{-- Booking Reference --}}
    <div style="background:#F2F4F3;padding:20px;border-radius:8px;margin-bottom:30px;border-left:4px solid #B17C65;">
        <p style="margin:0 0 8px 0;font-size:12px;text-transform:uppercase;color:#6B8E7A;font-weight:600;letter-spacing:0.5px;">
            Uw boekingsnummer
        </p>
        <p style="margin:0;font-size:28px;font-weight:700;color:#1B3A4B;letter-spacing:1px;">
            {{ $booking->reference }}
        </p>
        <p style="margin:10px 0 0 0;font-size:13px;color:#6B8E7A;">
            Bewaar dit nummer voor uw administratie en eventuele correspondentie.
        </p>
    </div>

    {{-- Trip Details --}}
    <h3 style="color:#1B3A4B;font-size:18px;margin:30px 0 15px 0;padding-bottom:10px;border-bottom:2px solid #F2F4F3;">
        📍 Uw reis
    </h3>

    <table width="100%" cellpadding="10" style="margin-bottom:25px;">
        <tr>
            <td style="background:#F2F4F3;border-radius:8px;padding:20px;">
                <p style="margin:0 0 5px 0;font-size:13px;color:#6B8E7A;font-weight:600;text-transform:uppercase;">
                    Reis
                </p>
                <p style="margin:0;font-size:18px;font-weight:600;color:#1B3A4B;">
                    {{ $booking->product->name }}
                </p>
            </td>
        </tr>
    </table>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:30px;">
        <tr>
            <td width="50%" style="padding-right:10px;">
                <div style="background:#F2F4F3;border-radius:8px;padding:15px;height:100%;">
                    <p style="margin:0 0 5px 0;font-size:12px;color:#6B8E7A;font-weight:600;text-transform:uppercase;">
                        📅 Vertrekdatum
                    </p>
                    <p style="margin:0;font-size:16px;font-weight:600;color:#1B3A4B;">
                        {{ $booking->departure_date_formatted }}
                    </p>
                </div>
            </td>
            <td width="50%" style="padding-left:10px;">
                <div style="background:#F2F4F3;border-radius:8px;padding:15px;height:100%;">
                    <p style="margin:0 0 5px 0;font-size:12px;color:#6B8E7A;font-weight:600;text-transform:uppercase;">
                        ⏱️ Duur
                    </p>
                    <p style="margin:0;font-size:16px;font-weight:600;color:#1B3A4B;">
                        {{ $booking->product->duration }} dagen
                    </p>
                </div>
            </td>
        </tr>
    </table>

    {{-- Travelers --}}
    <h3 style="color:#1B3A4B;font-size:18px;margin:30px 0 15px 0;padding-bottom:10px;border-bottom:2px solid #F2F4F3;">
        👥 Reizigers ({{ $booking->travelers->count() }} {{ $booking->travelers->count() === 1 ? 'persoon' : 'personen' }})
    </h3>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:30px;">
        @foreach($booking->travelers as $index => $traveler)
        <tr>
            <td style="padding-bottom:10px;">
                <div style="background:#F2F4F3;border-radius:8px;padding:15px;display:flex;align-items:center;">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="40">
                                <div style="width:32px;height:32px;background:#AFCB98;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#FFFFFF;font-weight:700;font-size:14px;">
                                    {{ $index + 1 }}
                                </div>
                            </td>
                            <td>
                                <p style="margin:0;font-size:15px;font-weight:600;color:#1B3A4B;">
                                    {{ $traveler->full_name }}
                                    @if($traveler->id === $booking->main_booker_id)
                                        <span style="background:#B17C65;color:#FFFFFF;font-size:11px;padding:3px 8px;border-radius:12px;margin-left:8px;font-weight:600;">
                                            HOOFDBOEKER
                                        </span>
                                    @endif
                                </p>
                                <p style="margin:3px 0 0 0;font-size:13px;color:#6B8E7A;">
                                    Geboortedatum: {{ $traveler->birthdate_formatted }}
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
        @endforeach
    </table>

    {{-- Contact Details --}}
    <h3 style="color:#1B3A4B;font-size:18px;margin:30px 0 15px 0;padding-bottom:10px;border-bottom:2px solid #F2F4F3;">
        📧 Uw contactgegevens
    </h3>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:30px;background:#F2F4F3;border-radius:8px;padding:20px;">
        <tr>
            <td>
                <p style="margin:0 0 15px 0;">
                    <span style="font-size:12px;color:#6B8E7A;font-weight:600;text-transform:uppercase;display:block;margin-bottom:5px;">
                        Naam
                    </span>
                    <span style="font-size:15px;color:#1B3A4B;">
                        {{ $booking->contact->name }}
                    </span>
                </p>
                <p style="margin:0 0 15px 0;">
                    <span style="font-size:12px;color:#6B8E7A;font-weight:600;text-transform:uppercase;display:block;margin-bottom:5px;">
                        E-mailadres
                    </span>
                    <span style="font-size:15px;color:#1B3A4B;">
                        {{ $booking->contact->email }}
                    </span>
                </p>
                <p style="margin:0 0 15px 0;">
                    <span style="font-size:12px;color:#6B8E7A;font-weight:600;text-transform:uppercase;display:block;margin-bottom:5px;">
                        Telefoonnummer
                    </span>
                    <span style="font-size:15px;color:#1B3A4B;">
                        {{ $booking->contact->phone }}
                    </span>
                </p>
                <p style="margin:0;">
                    <span style="font-size:12px;color:#6B8E7A;font-weight:600;text-transform:uppercase;display:block;margin-bottom:5px;">
                        Adres
                    </span>
                    <span style="font-size:15px;color:#1B3A4B;white-space:pre-line;">{{ $booking->contact->address }}</span>
                </p>
            </td>
        </tr>
    </table>

    {{-- Next Steps --}}
    <div style="background:#E8F5E9;border-left:4px solid #AFCB98;padding:20px;border-radius:8px;margin:30px 0;">
        <h3 style="color:#1B3A4B;font-size:18px;margin:0 0 15px 0;">
            🌿 Wat gebeurt er nu?
        </h3>
        <ul style="margin:0;padding:0 0 0 20px;color:#1B3A4B;">
            <li style="margin-bottom:10px;line-height:1.6;">
                <strong>Bevestiging:</strong> We hebben uw boeking in goede orde ontvangen en gaan deze voor u verwerken.
            </li>
            <li style="margin-bottom:10px;line-height:1.6;">
                <strong>Contact:</strong> Binnen 2 werkdagen nemen wij contact met u op voor de definitieve bevestiging en eventuele vragen.
            </li>
            <li style="margin-bottom:10px;line-height:1.6;">
                <strong>Reisdocumenten:</strong> U ontvangt uiterlijk 2 weken voor vertrek alle benodigde documenten en informatie.
            </li>
            <li style="margin-bottom:0;line-height:1.6;">
                <strong>Vragen?</strong> Neem gerust contact met ons op via onderstaande gegevens.
            </li>
        </ul>
    </div>

    {{-- Contact Info --}}
    <div style="margin:30px 0;padding:20px;background:#F8F9FA;border-radius:8px;">
        <h4 style="margin:0 0 12px 0;color:#1B3A4B;font-size:16px;">Heeft u vragen?</h4>
        <p style="margin:0 0 8px 0;font-size:14px;color:#1B3A4B;">
            📞 <strong>Telefoon:</strong> <a href="tel:{{ config('contact.phone') }}" style="color:#2F3E46;text-decoration:none;">{{ config('contact.phone') }}</a>
        </p>
        <p style="margin:0;font-size:14px;color:#1B3A4B;">
            📧 <strong>E-mail:</strong> <a href="mailto:{{ config('contact.mail') }}" style="color:#2F3E46;text-decoration:none;">{{ config('contact.mail') }}</a>
        </p>
    </div>

    {{-- Closing --}}
    <p style="margin:30px 0 5px 0;color:#1B3A4B;font-size:15px;">
        We kijken ernaar uit u te mogen verwelkomen op deze bijzondere reis!
    </p>

    <p style="margin:20px 0 0 0;color:#1B3A4B;font-size:15px;">
        Met vriendelijke groet,<br><br>
        <strong>{{ config('contact.full_name') }}</strong><br>
        {{ config('app.name') }}
    </p>
@endsection
