@extends('emails.default')

@section('preheader')
    {{ $subscriber->name ?: $subscriber->email }} heeft zich ingeschreven voor de nieuwsbrief.
@endsection

@section('content')
    @include('emails.partials.admin-intro', [
        'label' => 'Nieuwsbrief',
        'title' => 'Nieuwe inschrijving',
        'meta' => 'Ontvangen op ' . now()->setTimezone('Europe/Amsterdam')->format('d-m-Y \o\m H:i') . ' uur',
    ])

    @include('emails.partials.heading', ['text' => 'Abonnee'])

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
        @if ($subscriber->name)
            @include('emails.partials.detail-row', ['label' => 'Naam', 'value' => $subscriber->name, 'strong' => true])
        @endif
        @include('emails.partials.detail-row', ['label' => 'E-mail', 'value' => $subscriber->email, 'href' => 'mailto:' . $subscriber->email])
        @include('emails.partials.detail-row', ['label' => 'Status', 'value' => $subscriber->status_label])
    </table>
@endsection

@section('footer')
    <p style="margin:0;font-size:12px;line-height:1.7;color:#4d6f80;">
        Automatische notificatie van de nieuwsbriefinschrijving op {{ config('app.name') }}.
    </p>
@endsection
