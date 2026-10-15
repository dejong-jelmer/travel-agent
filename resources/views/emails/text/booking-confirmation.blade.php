@extends('emails.text.default')
@php
    $travelerCount = $booking->travelers->count();
    $isSolo = $travelerCount === 1;
@endphp
@section('content')
Beste {!! $booking->mainBooker->first_name !!},

Je boeking is binnen

Wat leuk dat {!! $isSolo ? 'je' : 'jullie' !!} met {!! config('app.name') !!} op pad {!! $isSolo ? 'gaat' : 'gaan' !!}. Hieronder vind je een overzicht van je boeking. Ik ga er nu voor je mee aan de slag.

Je boekingsnummer: {!! $booking->reference !!}
Handig voor de administratie of voor als we contact hebben.

== Je reis ==

Reis: {!! $booking->trip->name !!}
Vertrek: {!! $booking->departure_date_formatted !!}
Terugkomst: {!! $booking->return_date_formatted !!}
Duur: {!! $booking->trip->duration !!} dagen

== Reizigers ==

@foreach ($booking->travelers as $index => $traveler)
{!! $index + 1 !!}. {!! $traveler->full_name !!}{!! $traveler->id === $booking->main_booker_id ? ' · hoofdboeker' : '' !!}
   Geboren op {!! $traveler->birthdate_formatted !!}
@endforeach

== Je contactgegevens ==

Naam: {!! $booking->contact->name !!}
E-mailadres: {!! $booking->contact->email !!}
Telefoonnummer: {!! $booking->contact->phone !!}
Adres:
{!! $booking->contact->address !!}

Klopt er iets niet? Antwoord gewoon op deze e-mail, dan pas ik het aan.

== Wat gebeurt er nu? ==

@include('emails.text.partials.steps', ['steps' => [
    ['Ik ga aan de slag', 'Je boeking is bij mij binnen en ik ga alles voor je vastleggen.'],
    ['Je hoort van mij', 'Binnen drie werkdagen neem ik contact met je op voor de definitieve bevestiging en om eventuele vragen door te nemen.'],
    ['Je reisdocumenten', 'Uiterlijk twee weken voor vertrek ontvang je alles wat je nodig hebt: tickets, routebeschrijving en mijn persoonlijke tips voor onderweg.'],
]])

Heb je nu al een vraag? Bel of mail me gerust.
{!! config('contact.phone') !!} · {!! config('contact.mail') !!}

Hartelijke groet,

{!! config('contact.first_name') !!}
{!! config('app.name') !!}
@endsection
