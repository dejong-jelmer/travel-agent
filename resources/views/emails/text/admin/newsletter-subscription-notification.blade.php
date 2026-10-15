@extends('emails.text.default')
@section('content')
@include('emails.text.partials.admin-intro', [
    'label' => 'Nieuwsbrief',
    'title' => 'Nieuwe inschrijving',
    'meta' => 'Ontvangen op ' . now()->setTimezone('Europe/Amsterdam')->format('d-m-Y \o\m H:i') . ' uur',
])

== Abonnee ==

@if ($subscriber->name)
Naam: {!! $subscriber->name !!}
@endif
E-mail: {!! $subscriber->email !!}
Status: {!! $subscriber->status_label !!}
@endsection
@section('footer')
Automatische notificatie van de nieuwsbriefinschrijving op {!! config('app.name') !!}.
@endsection
