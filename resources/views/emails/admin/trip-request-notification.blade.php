@extends('emails.default')

@section('content')
    {{-- Alert Header --}}
    <div style="background:#ffffff;border-left:4px solid #afcb98;padding:20px;margin:30px 0;">
        <p style="margin:0;font-size:15px;color:#1e2d3d;line-height:1.8;">
            {{ __('trip_request.mail.notification_header') }}:
        </p>
        <p style="margin:0;font-size:15px;color:#1e2d3d;line-height:1.8;">
            {{ __('trip_request.mail.notification_subheader', ['trip' => $tripRequest->trip->name]) }}
        </p>
    </div>

    {{-- Timestamp --}}
    <div style="background:#fbfbf7;padding:16px;border-radius:8px;margin-bottom:30px;text-align:center;">
        <p style="margin:0;font-size:13px;color:#82b2ca;font-weight:600;">
            {{ __('trip_request.mail.notification_received_at') }}: {{ $tripRequest->created_at->setTimezone('Europe/Amsterdam')->format('d-m-Y \o\m H:i') }} uur
        </p>
    </div>

    {{-- Trip Details --}}
    <h3 style="color:#1e2d3d;font-size:20px;margin:35px 0 15px 0;padding-bottom:12px;">
        {{ __('trip_request.mail.notification_section_trip') }}
    </h3>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:30px;background:#FFFFFF;padding:10px;">
        <tr>
            <td>
                <table width="100%" cellpadding="12" cellspacing="0">
                    <tr>
                        <td width="30%" style="vertical-align:top;">
                            <p style="margin:0;font-size:12px;color:#1e2d3d;font-weight:700;letter-spacing:0.5px;">
                                {{ __('trip_request.mail.notification_label_trip') }}:
                            </p>
                        </td>
                        <td width="70%" style="vertical-align:top;">
                            <p style="margin:0;font-size:16px;font-weight:700;color:#1e2d3d;">
                                {{ $tripRequest->trip->name }} ({{ $tripRequest->trip->slug }})
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Contact Details --}}
    <h3 style="color:#1e2d3d;font-size:20px;margin:35px 0 15px 0;padding-bottom:12px;">
        {{ __('trip_request.mail.notification_section_contact') }}
    </h3>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:30px;background:#FFFFFF;padding:10px;">
        <tr>
            <td>
                <table width="100%" cellpadding="12" cellspacing="0">
                    <tr>
                        <td width="30%" style="vertical-align:top;">
                            <p style="margin:0;font-size:12px;color:#1e2d3d;font-weight:700;letter-spacing:0.5px;">
                                {{ __('trip_request.mail.notification_label_name') }}:
                            </p>
                        </td>
                        <td width="70%" style="vertical-align:top;">
                            <p style="margin:0;font-size:16px;font-weight:700;color:#1e2d3d;">
                                {{ $tripRequest->name }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top;padding-top:8px;">
                            <p style="margin:0;font-size:12px;color:#1e2d3d;font-weight:700;letter-spacing:0.5px;">
                                {{ __('trip_request.mail.notification_label_email') }}:
                            </p>
                        </td>
                        <td style="vertical-align:top;padding-top:8px;">
                            <p style="margin:0;font-size:15px;color:#30547e;">
                                <a href="mailto:{{ $tripRequest->email }}" style="color:#1e2d3d;text-decoration:none;font-weight:600;">
                                    {{ $tripRequest->email }}
                                </a>
                            </p>
                        </td>
                    </tr>
                    @if($tripRequest->phone)
                    <tr>
                        <td style="vertical-align:top;padding-top:8px;">
                            <p style="margin:0;font-size:12px;color:#1e2d3d;font-weight:700;letter-spacing:0.5px;">
                                {{ __('trip_request.mail.notification_label_phone') }}:
                            </p>
                        </td>
                        <td style="vertical-align:top;padding-top:8px;">
                            <p style="margin:0;font-size:15px;color:#1e2d3d;">
                                <a href="tel:{{ $tripRequest->phone }}" style="color:#1e2d3d;text-decoration:none;font-weight:600;">
                                    {{ $tripRequest->phone }}
                                </a>
                            </p>
                        </td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    {{-- Trip Preferences --}}
    <h3 style="color:#1e2d3d;font-size:20px;margin:35px 0 15px 0;padding-bottom:12px;">
        {{ __('trip_request.mail.notification_section_preferences') }}
    </h3>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:30px;background:#FFFFFF;padding:10px;">
        <tr>
            <td>
                <table width="100%" cellpadding="12" cellspacing="0">
                    <tr>
                        <td width="30%" style="vertical-align:top;">
                            <p style="margin:0;font-size:12px;color:#1e2d3d;font-weight:700;letter-spacing:0.5px;">
                                {{ __('trip_request.mail.notification_label_period') }}:
                            </p>
                        </td>
                        <td width="70%" style="vertical-align:top;">
                            <p style="margin:0;font-size:15px;color:#1e2d3d;">
                                {{ $tripRequest->preferred_month ?? '–' }}
                            </p>
                        </td>
                    </tr>
                    @if($tripRequest->preferred_period_note)
                    <tr>
                        <td style="vertical-align:top;padding-top:8px;">
                            <p style="margin:0;font-size:12px;color:#1e2d3d;font-weight:700;letter-spacing:0.5px;">
                                {{ __('trip_request.mail.notification_label_period_note') }}:
                            </p>
                        </td>
                        <td style="vertical-align:top;padding-top:8px;">
                            <p style="margin:0;font-size:15px;color:#1e2d3d;">
                                {{ $tripRequest->preferred_period_note }}
                            </p>
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td style="vertical-align:top;padding-top:8px;">
                            <p style="margin:0;font-size:12px;color:#1e2d3d;font-weight:700;letter-spacing:0.5px;">
                                {{ __('trip_request.mail.notification_label_travelers') }}:
                            </p>
                        </td>
                        <td style="vertical-align:top;padding-top:8px;">
                            <p style="margin:0;font-size:15px;color:#1e2d3d;">
                                @if($tripRequest->travelers_count === 9)
                                    {{ __('trip_request.mail.notification_travelers_more_than_8') }}
                                @elseif($tripRequest->travelers_count)
                                    {{ $tripRequest->travelers_count }}
                                @else
                                    {{ __('trip_request.mail.notification_not_specified') }}
                                @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top;padding-top:8px;">
                            <p style="margin:0;font-size:12px;color:#1e2d3d;font-weight:700;letter-spacing:0.5px;">
                                {{ __('trip_request.mail.notification_label_station') }}:
                            </p>
                        </td>
                        <td style="vertical-align:top;padding-top:8px;">
                            <p style="margin:0;font-size:15px;color:#1e2d3d;">
                                {{ $tripRequest->departure_station ?? __('trip_request.mail.notification_not_specified') }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Notes --}}
    <h3 style="color:#1e2d3d;font-size:20px;margin:35px 0 15px 0;padding-bottom:12px;">
        {{ __('trip_request.mail.notification_section_notes') }}
    </h3>

    <div style="background:#FFFFFF;margin-bottom:30px;">
        <p style="margin:0;font-size:15px;color:#1e2d3d;line-height:1.8;white-space:pre-wrap;">{{ $tripRequest->notes ?? __('trip_request.mail.notification_no_notes') }}</p>
    </div>

    {{-- Quick Reply Button --}}
    <div style="margin:30px 0;text-align:center;">
        <a href="mailto:{{ $tripRequest->email }}?subject=Re: {{ __('trip_request.mail.notification_reply_subject', ['trip' => $tripRequest->trip->name]) }}"
            style="display:inline-block;background:#2d5f6e;color:#FFFFFF;padding:16px 32px;text-decoration:none;border-radius:8px;font-size:16px;font-weight:600;box-shadow:0 2px 4px rgba(0,0,0,0.1);">
            {{ __('trip_request.mail.notification_reply_button') }}
        </a>
    </div>

    {{-- Footer Note --}}
    <p style="margin:30px 0 0 0;padding:20px;background:#fbfbf7;border-radius:8px;font-size:13px;color:#82b2ca;text-align:center;line-height:1.6;">
        Dit is een automatische notificatie van een reisaanvraag op {{ config('app.name') }}.<br>
        Reageer rechtstreeks naar de afzender, niet op deze e-mail.
    </p>
@endsection
