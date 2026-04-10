@extends('emails.default')

@section('content')
    {{-- Greeting --}}
    <h2 style="color:#1e2d3d;font-size:24px;margin:0 0 20px 0;font-weight:600;">
        {{ __('trip_request.mail.confirmation_greeting', ['name' => $tripRequest->name]) }}
    </h2>

    {{-- Confirmation message --}}
    <div style="background:#ffffff;border-left:4px solid #afcb98;padding:20px;margin:30px 0;">
        <p style="margin:0;font-size:15px;color:#1e2d3d;line-height:1.8;">
            {{ __('trip_request.mail.confirmation_intro', ['trip' => $tripRequest->trip->name]) }}
        </p>
    </div>

    {{-- Next steps --}}
    <p style="margin:20px 0;font-size:15px;color:#1e2d3d;line-height:1.8;">
        {{ __('trip_request.mail.confirmation_next_steps') }}
    </p>

    {{-- Summary of submitted request --}}
    <h3 style="color:#1e2d3d;font-size:18px;margin:35px 0 10px 0;font-weight:700;">
        {{ __('trip_request.mail.confirmation_summary_header') }}
    </h3>
    <p style="margin:0 0 15px 0;font-size:14px;color:#1e2d3d;line-height:1.8;">
        {{ __('trip_request.mail.confirmation_summary_intro') }}
    </p>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 30px 0;background:#fbfbf7;border-radius:6px;border:1px solid #dcc7aa;">
        <tr>
            <td style="padding:16px 20px;">
                <table width="100%" cellpadding="6" cellspacing="0">
                    <tr>
                        <td width="40%" style="vertical-align:top;font-size:13px;color:#82b2ca;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">
                            {{ __('trip_request.mail.notification_label_trip') }}
                        </td>
                        <td width="60%" style="vertical-align:top;font-size:14px;color:#1e2d3d;font-weight:600;">
                            {{ $tripRequest->trip->name }}
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top;font-size:13px;color:#82b2ca;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;padding-top:10px;">
                            {{ __('trip_request.mail.notification_label_period') }}
                        </td>
                        <td style="vertical-align:top;font-size:14px;color:#1e2d3d;padding-top:10px;">
                            {{ $tripRequest->preferred_month ?? __('trip_request.mail.notification_not_specified') }}
                        </td>
                    </tr>
                    @if($tripRequest->preferred_period_note)
                    <tr>
                        <td style="vertical-align:top;font-size:13px;color:#82b2ca;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;padding-top:10px;">
                            {{ __('trip_request.mail.notification_label_period_note') }}
                        </td>
                        <td style="vertical-align:top;font-size:14px;color:#1e2d3d;padding-top:10px;">
                            {{ $tripRequest->preferred_period_note }}
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td style="vertical-align:top;font-size:13px;color:#82b2ca;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;padding-top:10px;">
                            {{ __('trip_request.mail.notification_label_travelers') }}
                        </td>
                        <td style="vertical-align:top;font-size:14px;color:#1e2d3d;padding-top:10px;">
                            @if($tripRequest->travelers_count === 9)
                                {{ __('trip_request.mail.notification_travelers_more_than_8') }}
                            @elseif($tripRequest->travelers_count)
                                {{ $tripRequest->travelers_count }}
                            @else
                                {{ __('trip_request.mail.notification_not_specified') }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top;font-size:13px;color:#82b2ca;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;padding-top:10px;">
                            {{ __('trip_request.mail.notification_label_station') }}
                        </td>
                        <td style="vertical-align:top;font-size:14px;color:#1e2d3d;padding-top:10px;">
                            {{ $tripRequest->departure_station ?? __('trip_request.mail.notification_not_specified') }}
                        </td>
                    </tr>
                    @if($tripRequest->phone)
                    <tr>
                        <td style="vertical-align:top;font-size:13px;color:#82b2ca;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;padding-top:10px;">
                            {{ __('trip_request.mail.notification_label_phone') }}
                        </td>
                        <td style="vertical-align:top;font-size:14px;color:#1e2d3d;padding-top:10px;">
                            {{ $tripRequest->phone }}
                        </td>
                    </tr>
                    @endif
                    @if($tripRequest->notes)
                    <tr>
                        <td style="vertical-align:top;font-size:13px;color:#82b2ca;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;padding-top:10px;">
                            {{ __('trip_request.mail.notification_section_notes') }}
                        </td>
                        <td style="vertical-align:top;font-size:14px;color:#1e2d3d;padding-top:10px;white-space:pre-wrap;">{{ $tripRequest->notes }}</td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    {{-- What to expect --}}
    <h3 style="color:#1e2d3d;font-size:18px;margin:35px 0 10px 0;font-weight:700;">
        {{ __('trip_request.mail.confirmation_what_to_expect_header') }}
    </h3>
    <ul style="margin:0 0 25px 0;padding-left:22px;font-size:15px;color:#1e2d3d;line-height:1.8;">
        <li>{{ __('trip_request.mail.confirmation_what_to_expect_1') }}</li>
        <li>{{ __('trip_request.mail.confirmation_what_to_expect_2') }}</li>
        <li>{{ __('trip_request.mail.confirmation_what_to_expect_3') }}</li>
    </ul>

    {{-- Contact --}}
    <div style="margin:30px 0;padding:20px;background:#f5f0e8;">
        <p style="margin:0;font-size:14px;color:#1e2d3d;line-height:1.8;">
            {{ __('trip_request.mail.confirmation_question') }}
            <br>
            <strong>{{ __('trip_request.mail.confirmation_phone_label') }}</strong>
            <a href="tel:{{ config('contact.phone') }}" style="color:#1e2d3d;text-decoration:none;">
                {{ config('contact.phone') }}
            </a>
        </p>
    </div>

    {{-- Closing --}}
    <p style="margin:30px 0 0 0;color:#1e2d3d;font-size:15px;">
        {{ __('trip_request.mail.confirmation_closing') }}<br><br>
        <strong>{{ config('contact.first_name') }}</strong><br>
        {{ config('app.name') }}
    </p>
@endsection
