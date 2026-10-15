@extends('emails.text.default')
@php
    $adminUrl = route('admin.bookings.edit', $booking);
    $travelerCount = $booking->travelers->count();
    $adultCount = $booking->adults->count();
    $childCount = $booking->children->count();
    $statusLabel = $booking->status instanceof \UnitEnum && method_exists($booking->status, 'label')
        ? $booking->status->label()
        : null;
    $destinations = $booking->trip->destinations->isNotEmpty() ? $booking->trip->destinationsFormatted : null;
@endphp
@section('content')
@include('emails.text.partials.admin-intro', [
    'label' => 'Nieuwe boeking',
    'title' => $booking->trip->name,
    'meta' => 'Aangemaakt op ' . $booking->created_at_formatted . ' uur',
])

Boekingsnummer: {!! $booking->reference !!}
Openen in admin panel: {!! $adminUrl !!}

== Reis ==

Reis: {!! $booking->trip->name !!}{!! $destinations ? ' (' . $destinations . ')' : '' !!}
Vertrek: {!! $booking->departure_date_formatted !!} ({!! $booking->departure_date->diffForHumans() !!})
Duur: {!! $booking->trip->duration !!} dagen, {!! $booking->trip->duration - 1 !!} nachten

== Hoofdboeker en contactpersoon ==

Naam: {!! $booking->mainBooker->full_name !!}
E-mail: {!! $booking->contact->email !!}
Telefoon: {!! $booking->contact->phone !!}
Geboortedatum: {!! $booking->mainBooker->birthdate_formatted !!}
Adres:
{!! $booking->contact->address !!}

== Reizigers ==

{!! $travelerCount !!} {!! $travelerCount === 1 ? 'persoon' : 'personen' !!}: {!! $adultCount !!} {!! $adultCount === 1 ? 'volwassene' : 'volwassenen' !!}, {!! $childCount !!} {!! $childCount === 1 ? 'kind' : 'kinderen' !!}

@foreach ($booking->travelers as $index => $traveler)
{!! $index + 1 !!}. {!! $traveler->full_name !!}{!! $traveler->id === $booking->main_booker_id ? ' · hoofdboeker' : '' !!}
   {!! $traveler->type->label() !!} · {!! $traveler->birthdate_formatted !!} · {!! $traveler->nationality !!}
@endforeach

== Status ==

Voorwaarden geaccepteerd: {!! $booking->has_accepted_conditions ? 'Ja' : 'Nee' !!}
Bevestigd: {!! $booking->has_confirmed ? 'Ja' : 'Wacht op bevestiging' !!}
@if ($statusLabel)
Boekingsstatus: {!! $statusLabel !!}
@endif

== Wat nu? ==

@include('emails.text.partials.steps', ['steps' => [
    ['Controleer de boekingsgegevens', 'Kijk of alles volledig en juist is.'],
    ['Neem binnen 2 werkdagen contact op', 'Bel de klant voor de definitieve bevestiging.'],
    ['Markeer de boeking als bevestigd', 'Doe dit in het admin panel na het telefonisch contact.'],
    ['Verstuur de reisdocumenten', 'Uiterlijk 2 weken voor vertrek.'],
]])

Boeking openen:
{!! $adminUrl !!}
@endsection
@section('footer')
Automatische notificatie van een nieuwe boeking op {!! config('app.name') !!}.
@endsection
