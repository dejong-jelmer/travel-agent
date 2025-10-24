@extends('emails.default')

@section('content')
    {{-- Alert Header --}}
    <div style="background:#2F3E46;color:#FFFFFF;padding:24px;border-radius:8px;margin-bottom:30px;text-align:center;">
        <h2 style="margin:0 0 8px 0;font-size:26px;font-weight:700;">📧 Nieuw Contact Formulier Bericht</h2>
        <p style="margin:0;font-size:15px;opacity:0.95;">
            Er is zojuist een nieuw bericht binnengekomen via het contactformulier
        </p>
    </div>

    {{-- Timestamp --}}
    <div style="background:#F2F4F3;padding:16px;border-radius:8px;margin-bottom:30px;text-align:center;">
        <p style="margin:0;font-size:13px;color:#6B8E7A;font-weight:600;">
            Ontvangen op: {{ now()->setTimezone('Europe/Amsterdam')->format('d-m-Y \o\m H:i') }} uur
        </p>
    </div>

    {{-- Contact Person Details --}}
    <h3 style="color:#1B3A4B;font-size:20px;margin:35px 0 15px 0;padding-bottom:12px;border-bottom:3px solid #B17C65;font-weight:700;">
        👤 Afzender Gegevens
    </h3>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:30px;background:#FFF8F3;border-radius:8px;padding:20px;border:2px solid #B17C65;">
        <tr>
            <td>
                <table width="100%" cellpadding="12" cellspacing="0">
                    <tr>
                        <td width="30%" style="vertical-align:top;">
                            <p style="margin:0;font-size:12px;color:#B17C65;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">
                                Naam
                            </p>
                        </td>
                        <td width="70%" style="vertical-align:top;">
                            <p style="margin:0;font-size:16px;font-weight:700;color:#1B3A4B;">
                                {{ $contact->name }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top;padding-top:8px;">
                            <p style="margin:0;font-size:12px;color:#B17C65;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">
                                📧 E-mail
                            </p>
                        </td>
                        <td style="vertical-align:top;padding-top:8px;">
                            <p style="margin:0;font-size:15px;color:#1B3A4B;">
                                <a href="mailto:{{ $contact->email }}" style="color:#2F3E46;text-decoration:none;font-weight:600;">
                                    {{ $contact->email }}
                                </a>
                            </p>
                        </td>
                    </tr>
                    @if($contact->phone)
                    <tr>
                        <td style="vertical-align:top;padding-top:8px;">
                            <p style="margin:0;font-size:12px;color:#B17C65;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">
                                📞 Telefoon
                            </p>
                        </td>
                        <td style="vertical-align:top;padding-top:8px;">
                            <p style="margin:0;font-size:15px;color:#1B3A4B;">
                                <a href="tel:{{ $contact->phone }}" style="color:#2F3E46;text-decoration:none;font-weight:600;">
                                    {{ $contact->phone }}
                                </a>
                            </p>
                        </td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    {{-- Message Content --}}
    <h3 style="color:#1B3A4B;font-size:20px;margin:35px 0 15px 0;padding-bottom:12px;border-bottom:3px solid #AFCB98;font-weight:700;">
        💬 Bericht
    </h3>

    <div style="background:#FFFFFF;border:2px solid #AFCB98;border-radius:8px;padding:24px;margin-bottom:30px;">
        <p style="margin:0;font-size:15px;color:#1B3A4B;line-height:1.8;white-space:pre-wrap;">{{ $contact->text }}</p>
    </div>

    {{-- Action Required Box --}}
    <div style="background:#FFF3CD;border-left:4px solid #FFA500;padding:20px;border-radius:8px;margin:30px 0;">
        <h3 style="color:#1B3A4B;font-size:18px;margin:0 0 12px 0;font-weight:700;">
            ⚠️ Actie Vereist
        </h3>
        <ul style="margin:0;padding:0 0 0 20px;color:#1B3A4B;line-height:1.8;">
            <li style="margin-bottom:8px;">
                <strong>Beantwoord dit bericht</strong> zo spoedig mogelijk via het opgegeven e-mailadres
            </li>
            <li style="margin-bottom:8px;">
                <strong>Bel de afzender</strong> terug indien een telefoonnummer is opgegeven
            </li>
            <li style="margin-bottom:0;">
                <strong>Verwachte responstijd:</strong> Binnen 1-2 werkdagen
            </li>
        </ul>
    </div>

    {{-- Quick Reply Button --}}
    <div style="margin:30px 0;text-align:center;">
        <a href="mailto:{{ $contact->email }}?subject=Re: Uw bericht via {{ config('app.name') }}"
            style="display:inline-block;background:#2F3E46;color:#FFFFFF;padding:16px 32px;text-decoration:none;border-radius:8px;font-size:16px;font-weight:600;box-shadow:0 2px 4px rgba(0,0,0,0.1);">
            📧 Beantwoord Direct via E-mail
        </a>
    </div>

    {{-- Footer Note --}}
    <p style="margin:30px 0 0 0;padding:20px;background:#F8F9FA;border-radius:8px;font-size:13px;color:#6B8E7A;text-align:center;line-height:1.6;">
        Dit is een automatische notificatie van het contactformulier op {{ config('app.name') }}.<br>
        Reageer rechtstreeks naar de afzender, niet op deze e-mail.
    </p>
@endsection
