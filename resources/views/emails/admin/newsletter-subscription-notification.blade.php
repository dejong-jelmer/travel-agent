@extends('emails.default')

@section('content')
    {{-- Alert Header --}}
    <div style="background:#ffffff;border-left:4px solid #afcb98;padding:20px;margin:30px 0;">
        <p style="margin:0;font-size:15px;color:#1e2d3d;line-height:1.8;">
            Nieuwe Nieuwsbrief Inschrijving:
        </p>
        <p style="margin:0;font-size:15px;color:#1e2d3d;line-height:1.8;">
            Er heeft zich zojuist iemand ingeschreven voor de nieuwsbrief.
        </p>
    </div>

    {{-- Timestamp --}}
    <div style="background:#fbfbf7;padding:16px;border-radius:8px;margin-bottom:30px;text-align:center;">
        <p style="margin:0;font-size:13px;color:#82b2ca;font-weight:600;">
            Ontvangen op: {{ now()->setTimezone('Europe/Amsterdam')->format('d-m-Y \o\m H:i') }} uur
        </p>
    </div>

    {{-- Subscriber Details --}}
    <h3 style="color:#1e2d3d;font-size:20px;margin:35px 0 15px 0;padding-bottom:12px;">
        Abonnee Gegevens
    </h3>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:30px;background:#FFFFFF;padding:10px;">
        <tr>
            <td>
                <table width="100%" cellpadding="12" cellspacing="0">
                    @if($subscriber->name)
                    <tr>
                        <td width="30%" style="vertical-align:top;">
                            <p style="margin:0;font-size:12px;color:#1e2d3d;font-weight:700;letter-spacing:0.5px;">
                                Naam:
                            </p>
                        </td>
                        <td width="70%" style="vertical-align:top;">
                            <p style="margin:0;font-size:16px;font-weight:700;color:#1e2d3d;">
                                {{ $subscriber->name }}
                            </p>
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td width="30%" style="vertical-align:top;padding-top:8px;">
                            <p style="margin:0;font-size:12px;color:#1e2d3d;font-weight:700;letter-spacing:0.5px;">
                                E-mail:
                            </p>
                        </td>
                        <td width="70%" style="vertical-align:top;padding-top:8px;">
                            <p style="margin:0;font-size:15px;color:#30547e;">
                                <a href="mailto:{{ $subscriber->email }}" style="color:#1e2d3d;text-decoration:none;font-weight:600;">
                                    {{ $subscriber->email }}
                                </a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top;padding-top:8px;">
                            <p style="margin:0;font-size:12px;color:#1e2d3d;font-weight:700;letter-spacing:0.5px;">
                                Status:
                            </p>
                        </td>
                        <td style="vertical-align:top;padding-top:8px;">
                            <p style="margin:0;font-size:15px;color:#1e2d3d;">
                                {{ $subscriber->status_label }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Footer Note --}}
    <p style="margin:30px 0 0 0;padding:20px;background:#fbfbf7;border-radius:8px;font-size:13px;color:#82b2ca;text-align:center;line-height:1.6;">
        Dit is een automatische notificatie van de nieuwsbrief inschrijving op {{ config('app.name') }}.
    </p>
@endsection
