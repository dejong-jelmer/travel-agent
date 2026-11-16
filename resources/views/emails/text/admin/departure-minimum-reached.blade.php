@extends('emails.text.default')
@section('content')
@include('emails.text.partials.admin-intro', [
    'label' => __('departure.mail.minimum_reached.label'),
    'title' => $departure->trip->name . ' · ' . $departure->departs_on->format('d-m-Y'),
])

{!! __('departure.mail.minimum_reached.intro') !!}
{!! __('departure.mail.minimum_reached.action') !!}

== {!! __('departure.mail.section_departure') !!} ==

{!! __('departure.mail.label_trip') !!}: {!! $departure->trip->name !!}
{!! __('departure.mail.label_period') !!}: {!! $departure->departs_on->format('d-m-Y') !!} - {!! $departure->returns_on->format('d-m-Y') !!}
{!! __('departure.mail.label_confirmed') !!}: {!! __('departure.mail.confirmed_of_minimum', ['confirmed' => $departure->confirmedParticipants(), 'minimum' => $departure->min_participants]) !!}
{!! __('departure.mail.label_occupied') !!}: {!! __('departure.mail.occupied_of_maximum', ['occupied' => $departure->occupiedSeats(), 'maximum' => $departure->max_participants]) !!}
{!! __('departure.mail.label_decision_date') !!}: {!! $departure->decision_date->format('d-m-Y') !!}

{!! __('departure.mail.button') !!}:
{!! route('admin.trips.show', $departure->trip) !!}
@endsection
@section('footer')
{!! __('departure.mail.footer') !!}
@endsection
