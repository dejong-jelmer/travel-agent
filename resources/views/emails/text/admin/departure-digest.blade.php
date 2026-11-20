@extends('emails.text.default')
@section('content')
@include('emails.text.partials.admin-intro', [
    'label' => __('departure.mail.digest.label'),
    'title' => __('departure.mail.digest.title'),
])

{!! __('departure.mail.digest.intro') !!}
@foreach ($groups as $key => $departures)

== {!! __("departure.mail.digest.groups.{$key}.title") !!} ==
{!! __("departure.mail.digest.groups.{$key}.help") !!}

@foreach ($departures as $departure)
- {!! $departure->trip->name !!}, {!! $departure->departs_on->format('d-m-Y') !!} - {!! $departure->returns_on->format('d-m-Y') !!}
  {!! __('departure.mail.digest.row', ['confirmed' => $departure->confirmedParticipants(), 'minimum' => $departure->min_participants, 'occupied' => $departure->occupiedSeats(), 'maximum' => $departure->max_participants, 'date' => $departure->decision_date->format('d-m-Y')]) !!}
  {!! route('admin.departures.show', $departure) !!}
@endforeach
@endforeach

{!! __('departure.mail.digest.button') !!}:
{!! route('admin.departures.index') !!}
@endsection
@section('footer')
{!! __('departure.mail.digest.footer') !!}
@endsection
