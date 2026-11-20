@extends('emails.text.default')
@php
    $replySubject = 'Re: ' . __('trip_request.mail.notification_reply_subject', ['trip' => $tripRequest->trip->name]);
    $replyUrl = 'mailto:' . $tripRequest->email . '?subject=' . rawurlencode($replySubject);
@endphp
@section('content')
@include('emails.text.partials.admin-intro', [
    'label' => __('trip_request.mail.notification_header'),
    'title' => $tripRequest->name . ' · ' . $tripRequest->trip->name,
    'meta' => __('trip_request.mail.notification_received_at') . ' '
        . $tripRequest->created_at->setTimezone('Europe/Amsterdam')->format('d-m-Y \o\m H:i') . ' uur',
])

== {!! __('trip_request.mail.notification_section_contact') !!} ==

{!! __('trip_request.mail.notification_label_name') !!}: {!! $tripRequest->name !!}
{!! __('trip_request.mail.notification_label_email') !!}: {!! $tripRequest->email !!}
@if ($tripRequest->phone)
{!! __('trip_request.mail.notification_label_phone') !!}: {!! $tripRequest->phone !!}
@endif

== {!! __('trip_request.mail.notification_section_preferences') !!} ==

{!! __('trip_request.mail.notification_label_trip') !!}: {!! $tripRequest->trip->name !!} ({!! $tripRequest->trip->slug !!})
@foreach ($tripRequest->summaryRows() as $row)
{!! $row['label'] !!}: {!! $row['value'] !!}
@endforeach

== {!! __('trip_request.mail.notification_section_notes') !!} ==

{!! $tripRequest->notes ?: __('trip_request.mail.notification_no_notes') !!}

{!! __('trip_request.mail.notification_reply_button') !!}:
{!! $replyUrl !!}
@endsection
@section('footer')
Automatische notificatie van een reisaanvraag. Reageer rechtstreeks naar de afzender, niet op deze e-mail.
@endsection
