@extends('emails.default')

@section('content')
    {{-- Alert Header --}}
    <div style="background:#ffffff;border-left:4px solid #afcb98;padding:20px;margin:30px 0;">
        <p style="margin:0;font-size:15px;color:#1e2d3d;line-height:1.8;">
            Nieuw Contact Formulier Bericht:
        </p>
        <p style="margin:0;font-size:15px;color:#1e2d3d;line-height:1.8;">
            Er is zojuist een nieuw bericht binnengekomen via het contactformulier
        </p>
    </div>

    {{-- Timestamp --}}
    <div style="background:#fbfbf7;padding:16px;border-radius:8px;margin-bottom:30px;text-align:center;">
        <p style="margin:0;font-size:13px;color:#82b2ca;font-weight:600;">
            Ontvangen op: {{ now()->setTimezone('Europe/Amsterdam')->format('d-m-Y \o\m H:i') }} uur
        </p>
    </div>

    {{-- Contact Person Details --}}
    <h3 style="color:#1e2d3d;font-size:20px;margin:35px 0 15px 0;padding-bottom:12px;">
        Afzender Gegevens
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
                                {{ $contact->name }}
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
                                <a href="mailto:{{ $contact->email }}" style="color:#1e2d3d;text-decoration:none;font-weight:600;">
                                    {{ $contact->email }}
                                </a>
                            </p>
                        </td>
                    </tr>
                    @if($contact->phone)
                    <tr>
                        <td style="vertical-align:top;padding-top:8px;">
                            <p style="margin:0;font-size:12px;color:#1e2d3d;font-weight:700;letter-spacing:0.5px;">
                                Telefoon:
                            </p>
                        </td>
                        <td style="vertical-align:top;padding-top:8px;">
                            <p style="margin:0;font-size:15px;color:#1e2d3d;">
                                <a href="tel:{{ $contact->phone }}" style="color:#1e2d3d;text-decoration:none;font-weight:600;">
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
    <h3 style="color:#30547e;font-size:20px;margin:35px 0 15px 0;padding-bottom:12px;">
        Bericht
    </h3>

    <div style="background:#FFFFFF;margin-bottom:30px;">
        <p style="margin:0;font-size:15px;color:#1e2d3d;line-height:1.8;white-space:pre-wrap;">{{ $contact->text }}</p>
    </div>


    {{-- Quick Reply Button --}}
    <div style="margin:30px 0;text-align:center;">
        <a href="mailto:{{ $contact->email }}?subject=Re: Uw bericht via {{ config('app.name') }}&body={{ rawurlencode("\n\n---\nOp " . now()->setTimezone('Europe/Amsterdam')->format('d-m-Y \o\m H:i') . " schreef " . $contact->name . ":\n\n" . $contact->text) }}"
            style="display:inline-block;background:#2d5f6e;color:#FFFFFF;padding:16px 32px;text-decoration:none;border-radius:8px;font-size:16px;font-weight:600;box-shadow:0 2px 4px rgba(0,0,0,0.1);">
            Beantwoord Direct via E-mail
        </a>
    </div>

    {{-- Footer Note --}}
    <p style="margin:30px 0 0 0;padding:20px;background:#fbfbf7;border-radius:8px;font-size:13px;color:#82b2ca;text-align:center;line-height:1.6;">
        Dit is een automatische notificatie van het contactformulier op {{ config('app.name') }}.<br>
        Reageer rechtstreeks naar de afzender, niet op deze e-mail.
    </p>
@endsection
