@extends('emails.default')

@section('content')
    {{-- Greeting --}}
    <h2 style="color:#2F3E46;font-size:24px;margin:0 0 20px 0;font-weight:600;">
        Beste {{ $booking->mainBooker->full_name }},
    </h2>

    {{-- Success Message --}}
    <div style="background:#fbfbf7;border-left:4px solid #AFCB98;padding:20px;margin:30px 0;">
        <h3 style="margin:0 0 10px 0;font-size:20px;font-weight:600;">We hebben uw boeking succesvol ontvangen</h3>
        <p style="margin:0;font-size:14px;">
            Bedankt voor uw vertrouwen in {{ config('app.name') }}
        </p>
    </div>

    {{-- Booking Reference --}}
    <div style="background:#fbfbf7;padding:20px;margin-bottom:30px;border-left:4px solid #f0972d;">
        <p
            style="margin:0 0 8px 0;font-size:12px;text-transform:uppercase;color:#A3BCCB;font-weight:600;letter-spacing:0.5px;">
            Uw boekingsnummer
        </p>
        <p style="margin:0;font-size:28px;font-weight:700;color:#2F3E46;letter-spacing:1px;">
            {{ $booking->reference }}
        </p>
        <p style="margin:10px 0 0 0;font-size:13px;color:#2F3E46;">
            Bewaar dit nummer voor uw administratie en eventuele correspondentie.
        </p>
    </div>

    {{-- Trip Details --}}
    <h3 style="color:#2F3E46;font-size:18px;margin:30px 0 15px 0;padding-bottom:10px;border-bottom:2px solid #fbfbf7;">
        Uw reis
    </h3>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:30px;background:#fbfbf7;">
        <tr>
            <td style="padding:20px;border-bottom:1px solid #e0e0e0;" colspan="3">
                <p style="margin:0 0 5px 0;font-size:13px;color:#A3BCCB;font-weight:600;text-transform:uppercase;">
                    Reis
                </p>
                <p style="margin:0;font-size:18px;font-weight:600;color:#2F3E46;">
                    {{ $booking->trip->name }}
                </p>
            </td>
        </tr>
        <tr>
            <td style="padding:20px;width:50%;">
                <p style="margin:0 0 5px 0;font-size:13px;color:#A3BCCB;font-weight:600;text-transform:uppercase;">
                    Vertrekdatum
                </p>
                <p style="margin:0;font-size:16px;font-weight:600;color:#2F3E46;">
                    {{ $booking->departure_date_formatted }}
                </p>
            </td>
            <td style="padding:20px;width:50%;">
                <p style="margin:0 0 5px 0;font-size:13px;color:#A3BCCB;font-weight:600;text-transform:uppercase;">
                    Duur
                </p>
                <p style="margin:0;font-size:16px;font-weight:600;color:#2F3E46;">
                    {{ $booking->trip->duration }} dagen
                </p>
            </td>
        </tr>
    </table>

    {{-- Travelers --}}
    <h3 style="color:#2F3E46;font-size:18px;margin:30px 0 15px 0;padding-bottom:10px;border-bottom:2px solid #fbfbf7;">
        Reizigers ({{ $booking->travelers->count() }} {{ $booking->travelers->count() === 1 ? 'persoon' : 'personen' }})
    </h3>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:30px;">
        @foreach ($booking->travelers as $index => $traveler)
            <tr>
                <td style="padding-bottom:10px;">
                    <div style="background:#fbfbf7;padding:15px;display:flex;align-items:center;">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="40">
                                    <div
                                        style="width:32px;height:32px;background:#AFCB98;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#FFFFFF;font-weight:700;font-size:14px;">
                                        {{ $index + 1 }}
                                    </div>
                                </td>
                                <td>
                                    <p style="margin:0;font-size:15px;font-weight:600;color:#2F3E46;">
                                        {{ $traveler->full_name }}
                                        @if ($traveler->id === $booking->main_booker_id)
                                            <span
                                                style="background:#f0972d;color:#FFFFFF;font-size:11px;padding:3px 8px;border-radius:12px;margin-left:8px;font-weight:600;">
                                                HOOFDBOEKER
                                            </span>
                                        @endif
                                    </p>
                                    <p style="margin:3px 0 0 0;font-size:13px;color:#A3BCCB;">
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
    <h3 style="color:#2F3E46;font-size:18px;margin:30px 0 15px 0;padding-bottom:10px;border-bottom:2px solid #fbfbf7;">
        Uw contactgegevens
    </h3>

    <table width="100%" cellpadding="0" cellspacing="0"
        style="margin-bottom:30px;background:#fbfbf7;padding:20px;">
        <tr>
            <td>
                <p style="margin:0 0 15px 0;">
                    <span
                        style="font-size:12px;color:#A3BCCB;font-weight:600;text-transform:uppercase;display:block;margin-bottom:5px;">
                        Naam
                    </span>
                    <span style="font-size:15px;color:#2F3E46;">
                        {{ $booking->contact->name }}
                    </span>
                </p>
                <p style="margin:0 0 15px 0;">
                    <span
                        style="font-size:12px;color:#A3BCCB;font-weight:600;text-transform:uppercase;display:block;margin-bottom:5px;">
                        E-mailadres
                    </span>
                    <span style="font-size:15px;color:#2F3E46;">
                        {{ $booking->contact->email }}
                    </span>
                </p>
                <p style="margin:0 0 15px 0;">
                    <span
                        style="font-size:12px;color:#A3BCCB;font-weight:600;text-transform:uppercase;display:block;margin-bottom:5px;">
                        Telefoonnummer
                    </span>
                    <span style="font-size:15px;color:#2F3E46;">
                        {{ $booking->contact->phone }}
                    </span>
                </p>
                <p style="margin:0;">
                    <span
                        style="font-size:12px;color:#A3BCCB;font-weight:600;text-transform:uppercase;display:block;margin-bottom:5px;">
                        Adres
                    </span>
                    <span
                        style="font-size:15px;color:#2F3E46;white-space:pre-line;">{{ $booking->contact->address }}</span>
                </p>
            </td>
        </tr>
    </table>

    {{-- Next Steps --}}
    <div style="background:#fbfbf7;border-left:4px solid #AFCB98;padding:20px;margin:30px 0;">
        <h3 style="color:#2F3E46;font-size:18px;margin:0 0 15px 0;">
            Wat gebeurt er nu?
        </h3>
        <ul style="margin:0;padding:0 0 0 20px;color:#2F3E46;">
            <li style="margin-bottom:10px;line-height:1.6;">
                <strong>Bevestiging:</strong> We hebben uw boeking in goede orde ontvangen en gaan deze voor u verwerken.
            </li>
            <li style="margin-bottom:10px;line-height:1.6;">
                <strong>Contact:</strong> Binnen 2 werkdagen nemen wij contact met u op voor de definitieve bevestiging en
                eventuele vragen.
            </li>
            <li style="margin-bottom:10px;line-height:1.6;">
                <strong>Reisdocumenten:</strong> U ontvangt uiterlijk 2 weken voor vertrek alle benodigde documenten en
                informatie.
            </li>
            <li style="margin-bottom:0;line-height:1.6;">
                <strong>Vragen?</strong> Neem gerust contact met ons op via onderstaande gegevens.
            </li>
        </ul>
    </div>

    {{-- Contact Info --}}
    <div style="margin:30px 0;padding:20px;background:#fbfbf7;">
        <h4 style="margin:0 0 12px 0;color:#2F3E46;font-size:16px;">Heeft u vragen?</h4>
        <p style="margin:0 0 8px 0;font-size:14px;color:#2F3E46;">
            <strong>Telefoon:</strong> <a href="tel:{{ config('contact.phone') }}"
                style="color:#2F3E46;text-decoration:none;">{{ config('contact.phone') }}</a>
        </p>
        <p style="margin:0;font-size:14px;color:#2F3E46;">
            <strong>E-mail:</strong> <a href="mailto:{{ config('contact.mail') }}"
                style="color:#2F3E46;text-decoration:none;">{{ config('contact.mail') }}</a>
        </p>
    </div>

    {{-- Closing --}}
    <p style="margin:30px 0 5px 0;color:#2F3E46;font-size:15px;">
        We kijken ernaar uit u te mogen verwelkomen op deze bijzondere reis!
    </p>

    <p style="margin:20px 0 0 0;color:#2F3E46;font-size:15px;">
        Met vriendelijke groet,<br><br>
        <strong>{{ config('contact.full_name') }}</strong><br>
        {{ config('app.name') }}
    </p>
@endsection
