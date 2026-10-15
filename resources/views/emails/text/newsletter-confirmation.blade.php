@extends('emails.text.default')
@section('content')
Hallo {!! $subscriber->name ?? strtok($subscriber->email, '@') !!},

Welkom bij de nieuwsbrief

Wat leuk dat je erbij bent. Ik bouw {!! config('app.name') !!} op als reismaker die de bestemming als uitgangspunt neemt, met aandacht voor de plek en het verhaal. Via deze nieuwsbrief deel ik wat ik onderweg tegenkom en waar ik mee bezig ben.

Je hebt je ingeschreven met {!! $subscriber->email !!}. Bevestig je inschrijving via de link hieronder, dan ben je er helemaal bij.

Inschrijving bevestigen:
{!! route('newsletter.subscription.confirm', $subscriber->confirmation_token) !!}

Deze link is {!! config('newsletter.subscription.confirmation_expires_after') !!} uur geldig.

== Wat kun je verwachten? ==

- Nieuwe reizen die ik samenstel.
- Persoonlijke ervaringen en verhalen van onderweg.
- Achtergrond bij bestemmingen.

Ik gebruik je e-mailadres alleen voor de nieuwsbrief en deel het nooit met anderen. Je kunt je altijd weer uitschrijven:
{!! route('newsletter.subscription.unsubscribe', $subscriber->unsubscribe_token) !!}

Hartelijke groet,

{!! config('contact.first_name') !!}
{!! config('app.name') !!}
@endsection
