@extends('emails.default')

@section('content')
    <h3>Welkom bij onze nieuwsbrief!</h3>

    <p>Hallo {{ $subscriber->name ?? 'daar' }},</p>

    <p>
        Bedankt voor je inschrijving op onze nieuwsbrief! We zijn blij dat je erbij bent.<br>
        Je ontvangt vanaf nu regelmatig updates en verhalen met betrekking tot ons reisaanbod.
    </p>

    <p>
        Je hebt je ingeschreven met het e-mailadres: <strong>{{ $subscriber->email }}</strong>
    </p>

    <p>
        Om je aanmelding af te ronden vragen we je nog even je <strong>inschrijving te bevestigen</strong>:
    </p>

    <div style="margin: 30px 0; text-align: center;">
        <a href="{{ route('newsletter.confirm', $subscriber->confirmation_token) }}"
           style="font-size: 16px; background-color: #2F3E46; color: #F2F4F3; padding: 12px 30px; text-decoration: none; border-radius: 6px; display: inline-block;">
           Aanmeldig afronden
        </a>
    </div>
     <small>
        Deze link is {{ config('newsletter.confirmation_expires_after') }} uur geldig.
    </small>
    <p>
        <strong>Wat kun je verwachten?</strong>
    </p>
    <ul>
        <li>Regelmatige updates over onze nieuwste reisaanbod</li>
    </ul>

    <p>
        Wil je je afmelden? Dat kan altijd via de link onderaan onze nieuwsbrieven,
        of via <a href="{{ route('newsletter.unsubscribe', $subscriber->unsubscribe_token) }}">deze link</a>.
    </p>

    <p>
        Met vriendelijke groet,<br><br>
        {{ config('contact.full_name') }}<br>
        {{ config('app.name') }}
    </p>
@endsection
