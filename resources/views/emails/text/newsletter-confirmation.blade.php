Welkom bij onze nieuwsbrief!

Hallo {{ $subscriber->name ?? strtok($subscriber->email, '@') }},

Fijn dat je erbij bent. Ik bouw Omdat We Reizen op als reismaker die kiest voor de bestemming als het uitgangspunt — met aandacht voor de plek en het verhaal. Via deze nieuwsbrief deel ik wat ik onderweg tegenkom en waar ik mee bezig ben.

Je hebt je ingeschreven met het e-mailadres: {{ $subscriber->email }}

Om je aanmelding af te ronden vragen we je nog even je inschrijving te bevestigen via de onderstaande link.

Bevestig je aanmelding:
{{ route('newsletter.subscription.confirm', $subscriber->confirmation_token) }}

(Deze link is {{ config('newsletter.subscription.confirmation_expires_after') }} uur geldig.)

== Wat kun je verwachten? ==

- Updates over nieuwe reizen die ik samenstel.
- Persoonlijke ervaringen en verhalen.
- Achtergrond bij bestemmingen.

Privacy: we gebruiken je e-mailadres uitsluitend voor onze eigen nieuwsbrief en delen het nooit met derden. Je kunt je op elk moment weer uitschrijven.

Wil je je afmelden? Dat kan altijd via:
{{ route('newsletter.subscription.unsubscribe', $subscriber->unsubscribe_token) }}

Met vriendelijke groet,

{{ config('contact.full_name') }}
{{ config('app.name') }}
