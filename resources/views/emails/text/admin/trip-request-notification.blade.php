==========================================
{{ __('trip_request.mail.notification_header') }}
{{ __('trip_request.mail.notification_subheader', ['trip' => $tripRequest->trip->name]) }}
==========================================

{{ __('trip_request.mail.notification_received_at') }}: {{ $tripRequest->created_at->setTimezone('Europe/Amsterdam')->format('d-m-Y \o\m H:i') }} uur

-- {{ __('trip_request.mail.notification_section_trip') }} --

{{ __('trip_request.mail.notification_label_trip') }}: {{ $tripRequest->trip->name }} ({{ $tripRequest->trip->slug }})

-- {{ __('trip_request.mail.notification_section_contact') }} --

{{ __('trip_request.mail.notification_label_name') }}: {{ $tripRequest->name }}
{{ __('trip_request.mail.notification_label_email') }}: {{ $tripRequest->email }}
@if($tripRequest->phone)
{{ __('trip_request.mail.notification_label_phone') }}: {{ $tripRequest->phone }}
@endif

-- {{ __('trip_request.mail.notification_section_preferences') }} --

{{ __('trip_request.mail.notification_label_period') }}: {{ $tripRequest->preferred_month ?? '–' }}
@if($tripRequest->preferred_period_note)
{{ __('trip_request.mail.notification_label_period_note') }}: {{ $tripRequest->preferred_period_note }}
@endif
{{ __('trip_request.mail.notification_label_travelers') }}: @if($tripRequest->travelers_count === 9){{ __('trip_request.mail.notification_travelers_more_than_8') }}@elseif($tripRequest->travelers_count){{ $tripRequest->travelers_count }}@else{{ __('trip_request.mail.notification_not_specified') }}@endif

{{ __('trip_request.mail.notification_label_station') }}: {{ $tripRequest->departure_station ?? __('trip_request.mail.notification_not_specified') }}

-- {{ __('trip_request.mail.notification_section_notes') }} --

{{ $tripRequest->notes ?? __('trip_request.mail.notification_no_notes') }}

------------------------------------------
{{ __('trip_request.mail.notification_reply_button') }}: mailto:{{ $tripRequest->email }}?subject=Re: {{ __('trip_request.mail.notification_reply_subject', ['trip' => $tripRequest->trip->name]) }}
