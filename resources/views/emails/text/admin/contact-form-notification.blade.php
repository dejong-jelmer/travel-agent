@extends('emails.text.default')
@php
    $receivedAt = now()->setTimezone('Europe/Amsterdam')->format('d-m-Y \o\m H:i');
    $replySubject = 'Re: Je bericht via ' . config('app.name');
    $replyBody = "\n\n---\nOp " . $receivedAt . ' schreef ' . $contact->name . ":\n\n" . $contact->text;
    $replyUrl = 'mailto:' . $contact->email . '?subject=' . rawurlencode($replySubject) . '&body=' . rawurlencode($replyBody);
@endphp
@section('content')
@include('emails.text.partials.admin-intro', [
    'label' => 'Contactformulier',
    'title' => 'Nieuw bericht van ' . $contact->name,
    'meta' => 'Ontvangen op ' . $receivedAt . ' uur',
])

== Bericht ==

{!! $contact->text !!}

== Afzender ==

Naam: {!! $contact->name !!}
E-mail: {!! $contact->email !!}
@if ($contact->phone)
Telefoon: {!! $contact->phone !!}
@endif

Beantwoorden:
{!! $replyUrl !!}
@endsection
@section('footer')
Automatische notificatie van het contactformulier. Reageer rechtstreeks naar de afzender, niet op deze e-mail.
@endsection
