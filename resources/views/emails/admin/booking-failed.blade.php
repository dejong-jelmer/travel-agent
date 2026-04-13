@extends('emails.default')

@section('content')
    {{-- Alert Header --}}
    <div style="background:#ffffff;border-left:4px solid #dc3545;padding:20px;margin:30px 0;">
        <p style="margin:0;font-size:15px;color:#1e2d3d;line-height:1.8;">
            Boeking Mislukt:
        </p>
        <p style="margin:0;font-size:15px;color:#1e2d3d;line-height:1.8;">
            Er is een fout opgetreden bij het verwerken van een boeking. Actie vereist.
        </p>
    </div>

    {{-- Timestamp --}}
    <div style="background:#fbfbf7;padding:16px;border-radius:8px;margin-bottom:30px;text-align:center;">
        <p style="margin:0;font-size:13px;color:#82b2ca;font-weight:600;">
            Ontvangen op: {{ now()->format('d-m-Y H:i:s') }}
        </p>
    </div>

    {{-- Error Details --}}
    <h3 style="color:#1e2d3d;font-size:20px;margin:35px 0 15px 0;padding-bottom:12px;">
        Foutdetails
    </h3>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:30px;background:#FFFFFF;padding:10px;">
        <tr>
            <td>
                <table width="100%" cellpadding="12" cellspacing="0">
                    <tr>
                        <td width="30%" style="vertical-align:top;">
                            <p style="margin:0;font-size:12px;color:#1e2d3d;font-weight:700;letter-spacing:0.5px;">
                                Foutmelding:
                            </p>
                        </td>
                        <td width="70%" style="vertical-align:top;">
                            <p style="margin:0;font-size:15px;font-weight:600;color:#dc3545;">
                                {{ $event->errorContext }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top;padding-top:8px;">
                            <p style="margin:0;font-size:12px;color:#1e2d3d;font-weight:700;letter-spacing:0.5px;">
                                Technische details:
                            </p>
                        </td>
                        <td style="vertical-align:top;padding-top:8px;">
                            <p style="margin:0;font-size:13px;color:#1e2d3d;font-family:monospace;background:#fbfbf7;padding:10px;border-radius:4px;">
                                {{ $event->errorMessage }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Attempted Booking Details --}}
    <h3 style="color:#1e2d3d;font-size:20px;margin:35px 0 15px 0;padding-bottom:12px;">
        Geprobeerde Boeking
    </h3>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:30px;background:#FFFFFF;padding:10px;">
        <tr>
            <td>
                <table width="100%" cellpadding="12" cellspacing="0">
                    <tr>
                        <td width="30%" style="vertical-align:top;">
                            <p style="margin:0;font-size:12px;color:#1e2d3d;font-weight:700;letter-spacing:0.5px;">
                                Reis:
                            </p>
                        </td>
                        <td width="70%" style="vertical-align:top;">
                            <p style="margin:0;font-size:16px;font-weight:700;color:#1e2d3d;">
                                {{ $event->bookingDetails['trip_name'] }}
                            </p>
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
                                {{ $event->bookingDetails['date'] }}
                            </p>
                        </td>
                    </tr>
                    @if ($event->bookingDetails['email'])
                    <tr>
                        <td style="vertical-align:top;padding-top:8px;">
                            <p style="margin:0;font-size:12px;color:#1e2d3d;font-weight:700;letter-spacing:0.5px;">
                                E-mail klant:
                            </p>
                        </td>
                        <td style="vertical-align:top;padding-top:8px;">
                            <p style="margin:0;font-size:15px;color:#30547e;">
                                <a href="mailto:{{ $event->bookingDetails['email'] }}" style="color:#1e2d3d;text-decoration:none;font-weight:600;">
                                    {{ $event->bookingDetails['email'] }}
                                </a>
                            </p>
                        </td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    {{-- Action Required --}}
    <div style="background:#ffffff;border-left:4px solid #dc3545;padding:20px;margin:30px 0;">
        <p style="margin:0 0 12px 0;font-size:15px;color:#1e2d3d;font-weight:700;">
            Actie Vereist
        </p>
        <ul style="margin:0;padding:0 0 0 20px;color:#1e2d3d;line-height:1.8;">
            <li style="margin-bottom:8px;"><strong>Controleer de server logs</strong> voor meer technische details</li>
            <li style="margin-bottom:8px;"><strong>Neem contact op met de klant</strong> om de boeking handmatig te
                verwerken</li>
            <li><strong>Onderzoek de oorzaak</strong> van de fout om herhaling te voorkomen</li>
        </ul>
    </div>

    {{-- Footer Note --}}
    <p style="margin:30px 0 0 0;padding:20px;background:#fbfbf7;border-radius:8px;font-size:13px;color:#82b2ca;text-align:center;line-height:1.6;">
        Dit is een automatische foutmelding. Controleer de logs voor volledige stacktrace.
    </p>
@endsection
