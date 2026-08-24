@extends('emails.default')

@section('content')
    {{-- Greeting --}}
    <h2 style="color:#30547e;font-size:24px;margin:0 0 20px 0;font-weight:600;">
        Beste {{ $booking->mainBooker->first_name }},
    </h2>

    {{-- Success Message --}}
    <div style="background:#ffffff;border-left:4px solid #AFCB98;padding:20px;margin:30px 0;">
        <h3 style="margin:0 0 10px 0;font-size:20px;font-weight:600;">Je boeking is binnen</h3>
        <p style="margin:0;font-size:14px;">
            Wat leuk dat
            {{ $booking->travelers->count() === 1 ? 'je' : 'jullie' }} met
            {{ config('app.name') }} op reis
            {{ $booking->travelers->count() === 1 ? 'gaat' : 'gaan' }}.
        </p>
    </div>

    {{-- Booking Reference --}}
    <div style="background:#ffffff;padding:20px;margin-bottom:30px;border-left:4px solid #f59e0b;">
        <p
            style="margin:0 0 8px 0;font-size:12px;text-transform:uppercase;color:#4d6f80;font-weight:600;letter-spacing:0.5px;">
            Uw boekingsnummer
        </p>
        <p style="margin:0;font-size:28px;font-weight:700;color:#30547e;letter-spacing:1px;">
            {{ $booking->reference }}
        </p>
        <p style="margin:10px 0 0 0;font-size:13px;color:#30547e;">
            Bewaar dit nummer voor je administratie en onze verdere contactmomenten.
        </p>
    </div>

    {{-- Trip Details --}}
    <h3
        style="color:#30547e;font-size:18px;margin:30px 0 15px 0;padding-bottom:10px;border-bottom:2px solid #fbfbf7;text-transform:uppercase;">
        Je reis
    </h3>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:30px;background:#fbfbf7;">
        <tr>
            <td style="padding:20px;border-bottom:1px solid #e0e0e0;" colspan="3">
                <p style="margin:0 0 5px 0;font-size:13px;color:#4d6f80;font-weight:600;text-transform:uppercase;">
                    Reis
                </p>
                <p style="margin:0;font-size:18px;font-weight:600;color:#30547e;">
                    {{ $booking->trip->name }}
                </p>
            </td>
        </tr>
        <tr>
            <td style="padding:20px;width:50%;">
                <p style="margin:0 0 5px 0;font-size:13px;color:#4d6f80;font-weight:600;text-transform:uppercase;">
                    Vertrekdatum
                </p>
                <p style="margin:0;font-size:16px;font-weight:600;color:#30547e;">
                    {{ $booking->departure_date_formatted }}
                </p>
            </td>
            <td style="padding:20px;width:50%;">
                <p style="margin:0 0 5px 0;font-size:13px;color:#4d6f80;font-weight:600;text-transform:uppercase;">
                    Terugkomstdatum
                </p>
                <p style="margin:0;font-size:16px;font-weight:600;color:#30547e;">
                    {{ $booking->return_date_formatted }}
                </p>
                <p style="margin:0;font-size:16px;font-weight:600;color:#30547e;">
                    (Totaal {{ $booking->trip->duration }} dagen)
                </p>
            </td>
        </tr>
    </table>

    {{-- Travelers --}}
    <h3 style="color:#30547e;font-size:18px;margin:30px 0 15px 0;padding-bottom:10px;border-bottom:2px solid #fbfbf7;">
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
                                    <p style="margin:0;font-size:15px;font-weight:600;color:#30547e;">
                                        {{ $traveler->full_name }}
                                        @if ($traveler->id === $booking->main_booker_id)
                                            <span
                                                style="background:#f59e0b;color:#FFFFFF;font-size:11px;padding:3px 8px;border-radius:12px;margin-left:8px;font-weight:600;">
                                                HOOFDBOEKER
                                            </span>
                                        @endif
                                    </p>
                                    <p style="margin:3px 0 0 0;font-size:13px;color:#4d6f80;">
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
    <h3
        style="color:#30547e;font-size:18px;margin:30px 0 15px 0;padding-bottom:10px;border-bottom:2px solid #fbfbf7;text-transform:uppercase;">
        Je contactgegevens
    </h3>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:30px;background:#fbfbf7;padding:20px;">
        <tr>
            <td>
                <p style="margin:0 0 15px 0;">
                    <span
                        style="font-size:12px;color:#4d6f80;font-weight:600;text-transform:uppercase;display:block;margin-bottom:5px;">
                        Naam
                    </span>
                    <span style="font-size:15px;color:#30547e;">
                        {{ $booking->contact->name }}
                    </span>
                </p>
                <p style="margin:0 0 15px 0;">
                    <span
                        style="font-size:12px;color:#4d6f80;font-weight:600;text-transform:uppercase;display:block;margin-bottom:5px;">
                        E-mailadres
                    </span>
                    <span style="font-size:15px;color:#30547e;">
                        {{ $booking->contact->email }}
                    </span>
                </p>
                <p style="margin:0 0 15px 0;">
                    <span
                        style="font-size:12px;color:#4d6f80;font-weight:600;text-transform:uppercase;display:block;margin-bottom:5px;">
                        Telefoonnummer
                    </span>
                    <span style="font-size:15px;color:#30547e;">
                        {{ $booking->contact->phone }}
                    </span>
                </p>
                <p style="margin:0;">
                    <span
                        style="font-size:12px;color:#4d6f80;font-weight:600;text-transform:uppercase;display:block;margin-bottom:5px;">
                        Adres
                    </span>
                    <span
                        style="font-size:15px;color:#30547e;white-space:pre-line;">{{ $booking->contact->address }}</span>
                </p>
            </td>
        </tr>
    </table>

    {{-- Next Steps --}}
    <div style="background:#ffffff;border-left:4px solid #AFCB98;padding:20px;margin:30px 0;">
        <h3 style="color:#30547e;font-size:18px;margin:0 0 15px 0;text-transform:uppercase;">
            Wat gebeurt er nu?
        </h3>
        <ol style="margin:0;padding:0 0 0 20px;color:#30547e;">
            <li style="margin-bottom:10px;line-height:1.6;">
                <strong>Ik ga aan de slag</strong><br>
                Je boeking is bij mij binnen en ik ga nu alles voor je vastleggen.
            </li>
            <li style="margin-bottom:10px;line-height:1.6;">
                <strong>Je hoort van mij</strong><br>
                Binnen drie werkdagen hoor je van mij voor de definitieve bevestiging en om eventuele vragen door te nemen.
            </li>
            <li style="margin-bottom:10px;line-height:1.6;">
                <strong>Je reisdocumenten</strong><br>
                Uiterlijk twee weken voor vertrek stuur ik je alles wat je nodig hebt:
                tickets, routebeschrijving en mijn persoonlijke tips voor onderweg.
            </li>
        </ol>
    </div>

    {{-- Contact Info --}}
    <div style="margin:30px 0;padding:20px;background:#fbfbf7;">
        <h4 style="margin:0 0 12px 0;color:#30547e;font-size:16px;text-transform:uppercase;">
            HEB JE NU AL EEN VRAAG?
        </h4>
        <p style="margin:0 0 8px 0;font-size:14px;color:#30547e;">
            Mail of bel gerust.
        </p>
        <p style="margin:0 0 8px 0;font-size:14px;color:#30547e;">
            <strong>Telefoon:</strong> <a href="tel:{{ config('contact.phone') }}"
                style="color:#30547e;text-decoration:none;">{{ config('contact.phone') }}</a>
        </p>
        <p style="margin:0;font-size:14px;color:#30547e;">
            <strong>E-mail:</strong> <a href="mailto:{{ config('contact.mail') }}"
                style="color:#30547e;text-decoration:none;">{{ config('contact.mail') }}</a>
        </p>
    </div>

    {{-- Closing --}}
    <p style="margin:30px 0 5px 0;color:#30547e;font-size:15px;">
        Ik ga voor je aan de slag en laat snel van me horen.
    </p>

    <p style="margin:20px 0 0 0;color:#30547e;font-size:15px;">
        Met vriendelijke groet,<br><br>
        <strong>{{ config('contact.first_name') }}</strong><br>
        {{ config('app.name') }}
        <br><br>
        Meer mens. Omdat we reizen.
    </p>
@endsection
