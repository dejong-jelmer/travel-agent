@extends('emails.text.default')
@section('content')
{!! __('trip_request.mail.confirmation_greeting', ['name' => $tripRequest->name]) !!}

{!! __('trip_request.mail.confirmation_title') !!}

{!! __('trip_request.mail.confirmation_intro', ['trip' => $tripRequest->trip->name]) !!}
@if ($tripRequest->departure)

{!! __($tripRequest->interest_only ? 'trip_request.mail.confirmation_interest_note' : 'trip_request.mail.confirmation_group_note') !!}
@endif

== {!! __('trip_request.mail.confirmation_summary_header') !!} ==

{!! __('trip_request.mail.confirmation_summary_intro') !!}

{!! __('trip_request.mail.notification_label_trip') !!}: {!! $tripRequest->trip->name !!}
@foreach ($tripRequest->summaryRows() as $row)
{!! $row['label'] !!}: {!! $row['value'] !!}
@endforeach
@if ($tripRequest->phone)
{!! __('trip_request.mail.notification_label_phone') !!}: {!! $tripRequest->phone !!}
@endif
@if ($tripRequest->notes)
{!! __('trip_request.mail.notification_section_notes') !!}:
{!! $tripRequest->notes !!}
@endif

== {!! __('trip_request.mail.confirmation_what_to_expect_header') !!} ==

@php($expect = $tripRequest->departure ? 'confirmation_what_to_expect_group_' : 'confirmation_what_to_expect_')
@include('emails.text.partials.steps', ['steps' => [
    __("trip_request.mail.{$expect}1"),
    __("trip_request.mail.{$expect}2"),
    __("trip_request.mail.{$expect}3"),
]])

{!! __('trip_request.mail.confirmation_question') !!}
{!! config('contact.phone') !!} · {!! config('contact.mail') !!}

{!! __('trip_request.mail.confirmation_closing') !!}

{!! config('contact.first_name') !!}
{!! config('app.name') !!}
@endsection
