@extends('emails.text.default')
@php
    $notSpecified = __('trip_request.mail.notification_not_specified');

    if ($tripRequest->travelers_count === 9) {
        $travelers = __('trip_request.mail.notification_travelers_more_than_8');
    } elseif ($tripRequest->travelers_count) {
        $travelers = (string) $tripRequest->travelers_count;
    } else {
        $travelers = $notSpecified;
    }
@endphp
@section('content')
{!! __('trip_request.mail.confirmation_greeting', ['name' => $tripRequest->name]) !!}

{!! __('trip_request.mail.confirmation_title') !!}

{!! __('trip_request.mail.confirmation_intro', ['trip' => $tripRequest->trip->name]) !!}

== {!! __('trip_request.mail.confirmation_summary_header') !!} ==

{!! __('trip_request.mail.confirmation_summary_intro') !!}

{!! __('trip_request.mail.notification_label_trip') !!}: {!! $tripRequest->trip->name !!}
{!! __('trip_request.mail.notification_label_period') !!}: {!! $tripRequest->preferred_month ?? $notSpecified !!}
@if ($tripRequest->preferred_period_note)
{!! __('trip_request.mail.notification_label_period_note') !!}: {!! $tripRequest->preferred_period_note !!}
@endif
{!! __('trip_request.mail.notification_label_travelers') !!}: {!! $travelers !!}
{!! __('trip_request.mail.notification_label_station') !!}: {!! $tripRequest->departure_station ?? $notSpecified !!}
@if ($tripRequest->phone)
{!! __('trip_request.mail.notification_label_phone') !!}: {!! $tripRequest->phone !!}
@endif
@if ($tripRequest->notes)
{!! __('trip_request.mail.notification_section_notes') !!}:
{!! $tripRequest->notes !!}
@endif

== {!! __('trip_request.mail.confirmation_what_to_expect_header') !!} ==

@include('emails.text.partials.steps', ['steps' => [
    __('trip_request.mail.confirmation_what_to_expect_1'),
    __('trip_request.mail.confirmation_what_to_expect_2'),
    __('trip_request.mail.confirmation_what_to_expect_3'),
]])

{!! __('trip_request.mail.confirmation_question') !!}
{!! config('contact.phone') !!} · {!! config('contact.mail') !!}

{!! __('trip_request.mail.confirmation_closing') !!}

{!! config('contact.first_name') !!}
{!! config('app.name') !!}
@endsection
