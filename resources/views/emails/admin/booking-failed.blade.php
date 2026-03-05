@extends('emails.default')

@section('content')
    {{-- Alert Header --}}
    <div style="background:#fff5f5;border-left:4px solid #dc3545;padding:20px;margin:30px 0;">
        <h2 style="margin:0 0 8px 0;font-size:26px;font-weight:700;color:#dc3545;">Boeking Mislukt</h2>
        <p style="margin:0;font-size:15px;color:#30547e;">
            Er is een fout opgetreden bij het verwerken van een boeking. Actie vereist.
        </p>
    </div>

    {{-- Error Details --}}
    <div style="background:#fff5f5;border-left:4px solid #dc3545;padding:20px;margin:30px 0;">
        <p style="margin:0 0 8px 0;font-size:13px;text-transform:uppercase;color:#A3BCCB;letter-spacing:0.5px;">
            Foutmelding
        </p>
        <p style="margin:0 0 16px 0;font-size:15px;font-weight:700;color:#dc3545;">
            {{ $event->errorContext }}
        </p>
        <p style="margin:0 0 8px 0;font-size:13px;text-transform:uppercase;color:#A3BCCB;letter-spacing:0.5px;">
            Technische details
        </p>
        <p style="margin:0;font-size:13px;color:#30547e;font-family:monospace;background:#fbfbf7;padding:10px;">
            {{ $event->errorMessage }}
        </p>
    </div>

    {{-- Attempted Booking Details --}}
    <h3 style="color:#30547e;font-size:20px;margin:35px 0 15px 0;padding-bottom:12px;border-bottom:3px solid #AFCB98;font-weight:700;">
        Geprobeerde Boeking
    </h3>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:25px;">
        <tr>
            <td style="background:#fbfbf7;padding:20px;border-left:4px solid #AFCB98;">
                <p style="margin:0 0 8px 0;font-size:13px;color:#AFCB98;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">
                    Reis
                </p>
                <p style="margin:0 0 15px 0;font-size:22px;font-weight:700;color:#30547e;">
                    {{ $event->bookingData->trip->name }}
                </p>
                <p style="margin:0 0 8px 0;font-size:13px;color:#AFCB98;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">
                    Vertrekdatum
                </p>
                <p style="margin:0;font-size:18px;font-weight:700;color:#30547e;">
                    {{ $event->bookingData->date->format('d-m-Y') }}
                </p>
            </td>
        </tr>
    </table>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:25px;">
        <tr>
            <td width="48%" style="padding-right:10px;">
                <div style="background:#fbfbf7;padding:18px;border-left:4px solid #f59e0b;">
                    <p style="margin:0 0 8px 0;font-size:12px;color:#A3BCCB;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">
                        Tijdstip
                    </p>
                    <p style="margin:0;font-size:15px;font-weight:700;color:#30547e;">
                        {{ now()->format('d-m-Y H:i:s') }}
                    </p>
                </div>
            </td>
            <td width="4%"></td>
            <td width="48%" style="padding-left:10px;">
                <div style="background:#fbfbf7;padding:18px;border-left:4px solid #f59e0b;">
                    <p style="margin:0 0 8px 0;font-size:12px;color:#A3BCCB;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">
                        Aantal reizigers
                    </p>
                    <p style="margin:0;font-size:15px;font-weight:700;color:#30547e;">
                        {{ count($event->bookingData->travelers) }}
                    </p>
                </div>
            </td>
        </tr>
    </table>

    {{-- Contact Details --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:30px;background:#fbfbf7;padding:20px;border:2px solid #f59e0b;">
        <tr>
            <td width="50%" style="padding-right:15px;border-right:2px solid #f0ede8;">
                <p style="margin:0 0 6px 0;font-size:12px;color:#f59e0b;font-weight:700;text-transform:uppercase;">
                    E-mail klant
                </p>
                <p style="margin:0;font-size:15px;color:#30547e;">
                    <a href="mailto:{{ $event->bookingData->contact->email }}" style="color:#30547e;text-decoration:none;font-weight:600;">
                        {{ $event->bookingData->contact->email }}
                    </a>
                </p>
            </td>
            <td width="50%" style="padding-left:15px;">
                <p style="margin:0 0 6px 0;font-size:12px;color:#f59e0b;font-weight:700;text-transform:uppercase;">
                    Telefoon klant
                </p>
                <p style="margin:0;font-size:15px;color:#30547e;">
                    <a href="tel:{{ $event->bookingData->contact->phone }}" style="color:#30547e;text-decoration:none;font-weight:600;">
                        {{ $event->bookingData->contact->phone }}
                    </a>
                </p>
            </td>
        </tr>
    </table>

    {{-- Action Required --}}
    <div style="background:#fff5f5;border-left:4px solid #dc3545;padding:20px;margin:30px 0;">
        <h3 style="color:#dc3545;font-size:18px;margin:0 0 12px 0;font-weight:700;">
            Actie Vereist
        </h3>
        <ul style="margin:0;padding:0 0 0 20px;color:#30547e;line-height:1.8;">
            <li style="margin-bottom:8px;"><strong>Controleer de server logs</strong> voor meer technische details</li>
            <li style="margin-bottom:8px;"><strong>Neem contact op met de klant</strong> om de boeking handmatig te verwerken</li>
            <li><strong>Onderzoek de oorzaak</strong> van de fout om herhaling te voorkomen</li>
        </ul>
    </div>

    <p style="margin:30px 0 0 0;padding:20px;background:#fbfbf7;font-size:13px;color:#A3BCCB;text-align:center;line-height:1.6;">
        Dit is een automatische foutmelding. Controleer de logs voor volledige stacktrace.
    </p>
@endsection
