@extends('emails.text.default')
@section('content')
@include('emails.text.partials.admin-intro', [
    'label' => 'Boeking mislukt',
    'title' => 'Er ging iets mis bij een boeking',
    'meta' => 'Ontvangen op ' . now()->setTimezone('Europe/Amsterdam')->format('d-m-Y \o\m H:i') . ' uur',
])

== Foutdetails ==

Foutmelding: {!! $errorContext !!}

Technische details:
{!! $errorMessage !!}

== Geprobeerde boeking ==

Reis: {!! $bookingDetails['trip_name'] !!}
Vertrekdatum: {!! $bookingDetails['date'] !!}
@if ($bookingDetails['email'])
E-mail klant: {!! $bookingDetails['email'] !!}
@endif

== Wat nu? ==

@include('emails.text.partials.steps', ['steps' => [
    ['Controleer de serverlogs', 'Daar vind je de volledige stacktrace.'],
    ['Neem contact op met de klant', 'Verwerk de boeking zo nodig handmatig in het admin panel.'],
    ['Zoek de oorzaak', 'Zodat deze fout niet nog een keer optreedt.'],
]])
@endsection
@section('footer')
Automatische foutmelding van {!! config('app.name') !!}.
@endsection
