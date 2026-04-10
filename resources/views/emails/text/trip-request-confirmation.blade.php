{{ __('trip_request.mail.confirmation_greeting', ['name' => $tripRequest->name]) }}

{{ __('trip_request.mail.confirmation_intro', ['trip' => $tripRequest->trip->name]) }}

{{ __('trip_request.mail.confirmation_next_steps') }}

== {{ __('trip_request.mail.confirmation_summary_header') }} ==

{{ __('trip_request.mail.confirmation_summary_intro') }}

{{ __('trip_request.mail.notification_label_trip') }}: {{ $tripRequest->trip->name }}
{{ __('trip_request.mail.notification_label_period') }}: {{ $tripRequest->preferred_month ?? __('trip_request.mail.notification_not_specified') }}
@if($tripRequest->preferred_period_note)
{{ __('trip_request.mail.notification_label_period_note') }}: {{ $tripRequest->preferred_period_note }}
@endif
{{ __('trip_request.mail.notification_label_travelers') }}: @if($tripRequest->travelers_count === 9){{ __('trip_request.mail.notification_travelers_more_than_8') }}@elseif($tripRequest->travelers_count){{ $tripRequest->travelers_count }}@else{{ __('trip_request.mail.notification_not_specified') }}@endif

{{ __('trip_request.mail.notification_label_station') }}: {{ $tripRequest->departure_station ?? __('trip_request.mail.notification_not_specified') }}
@if($tripRequest->phone)
{{ __('trip_request.mail.notification_label_phone') }}: {{ $tripRequest->phone }}
@endif
@if($tripRequest->notes)

{{ __('trip_request.mail.notification_section_notes') }}:
{{ $tripRequest->notes }}
@endif

== {{ __('trip_request.mail.confirmation_what_to_expect_header') }} ==

- {{ __('trip_request.mail.confirmation_what_to_expect_1') }}
- {{ __('trip_request.mail.confirmation_what_to_expect_2') }}
- {{ __('trip_request.mail.confirmation_what_to_expect_3') }}

{{ __('trip_request.mail.confirmation_question') }}
{{ __('trip_request.mail.confirmation_phone_label') }}{{ config('contact.phone') }}

{{ __('trip_request.mail.confirmation_closing') }}

{{ config('contact.first_name') }}
{{ config('app.name') }}
