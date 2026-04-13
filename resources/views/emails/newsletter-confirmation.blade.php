@extends('emails.default')

@section('content')
    <h3 style="color:#1e2d3d;font-size:22px;margin:0 0 20px 0;font-weight:700;">Welkom bij onze nieuwsbrief!</h3>

    <p style="font-size:15px;color:#1e2d3d;line-height:1.8;">Hallo {{ $subscriber->name ?? strtok($subscriber->email, '@') }},</p>

    <p style="font-size:15px;color:#1e2d3d;line-height:1.8;">
        Wat leuk dat je erbij bent. <br>
        Ik bouw Omdat We Reizen op als reismaker
        die kiest voor de bestemming als het uitgangspunt — met aandacht voor de plek en het verhaal.
        Via deze nieuwsbrief deel ik wat ik onderweg tegenkom en waar ik mee bezig ben.
    </p>

    <p style="font-size:15px;color:#1e2d3d;line-height:1.8;">
        Je hebt je ingeschreven met het e-mailadres: <strong>{{ $subscriber->email }}</strong>
    </p>

    <p style="font-size:15px;color:#1e2d3d;line-height:1.8;">
        Om je aanmelding af te ronden vragen we je nog even je <strong>inschrijving te bevestigen</strong> via de
        knop hieronder.
    </p>

    <div style="margin: 30px 0; text-align: center;">
        <a href="{{ route('newsletter.subscription.confirm', $subscriber->confirmation_token) }}"
            style="font-size: 16px; background-color: #30547e; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 6px; display: inline-block;">
            Aanmelding afronden
        </a>
    </div>
    <p style="font-size:13px;color:#82b2ca;text-align:center;margin:0 0 25px 0;">
        Deze link is {{ config('newsletter.subscription.confirmation_expires_after') }} uur geldig.
    </p>

    <h4 style="color:#1e2d3d;font-size:17px;margin:30px 0 10px 0;font-weight:700;">Wat kun je verwachten?</h4>
    <ul style="margin:0 0 25px 0;padding-left:22px;font-size:15px;color:#1e2d3d;line-height:1.8;">
        <li>Updates over nieuwe reizen die ik samenstel.</li>
        <li>Persoonlijke ervaringen en verhalen.</li>
        <li>Achtergrond bij bestemmingen.</li>
    </ul>

    <p style="font-size:14px;color:#1e2d3d;line-height:1.8;">
        <strong>Privacy:</strong> we gebruiken je e-mailadres uitsluitend voor onze eigen nieuwsbrief en delen
        het nooit met derden. Je kunt je op elk moment weer uitschrijven.
    </p>

    <p style="font-size:14px;color:#1e2d3d;line-height:1.8;">
        Wil je je afmelden? Dat kan altijd via <a href="{{ route('newsletter.subscription.unsubscribe', $subscriber->unsubscribe_token) }}"
            style="color:#30547e;">deze link</a>.
    </p>

    <p style="font-size:15px;color:#1e2d3d;line-height:1.8;margin-top:30px;">
        Met vriendelijke groet,<br><br>
        <strong>{{ config('contact.full_name') }}</strong><br>
        {{ config('app.name') }}
    </p>
@endsection
