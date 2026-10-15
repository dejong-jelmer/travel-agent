@extends('emails.default')

@php
    $receivedAt = now()->setTimezone('Europe/Amsterdam')->format('d-m-Y \o\m H:i');
    $replySubject = 'Re: Je bericht via ' . config('app.name');
    $replyBody = "\n\n---\nOp " . $receivedAt . ' schreef ' . $contact->name . ":\n\n" . $contact->text;
    $replyUrl = 'mailto:' . $contact->email . '?subject=' . rawurlencode($replySubject) . '&body=' . rawurlencode($replyBody);
@endphp

@section('preheader')
    {{ $contact->name }}: {{ Str::limit($contact->text, 90) }}
@endsection

@section('content')
    @include('emails.partials.admin-intro', [
        'label' => 'Contactformulier',
        'title' => 'Nieuw bericht van ' . $contact->name,
        'meta' => 'Ontvangen op ' . $receivedAt . ' uur',
    ])

    {{-- Message --}}
    @include('emails.partials.heading', ['text' => 'Bericht'])

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-left:3px solid #afcb98;">
        <tr>
            <td style="padding:4px 0 4px 20px;font-size:15px;line-height:1.7;color:#1e2d3d;white-space:pre-wrap;">{{ $contact->text }}</td>
        </tr>
    </table>

    {{-- Sender --}}
    @include('emails.partials.heading', ['text' => 'Afzender'])

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
        @include('emails.partials.detail-row', ['label' => 'Naam', 'value' => $contact->name, 'strong' => true])
        @include('emails.partials.detail-row', ['label' => 'E-mail', 'value' => $contact->email, 'href' => 'mailto:' . $contact->email])
        @if ($contact->phone)
            @include('emails.partials.detail-row', ['label' => 'Telefoon', 'value' => $contact->phone, 'href' => 'tel:' . $contact->phone])
        @endif
    </table>

    {{-- Quick reply --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center" style="padding:36px 0 0 0;">
                @include('emails.partials.button', ['url' => $replyUrl, 'text' => 'Beantwoorden'])
            </td>
        </tr>
    </table>
@endsection

@section('footer')
    <p style="margin:0;font-size:12px;line-height:1.7;color:#4d6f80;">
        Automatische notificatie van het contactformulier. Reageer rechtstreeks naar de afzender, niet op deze e-mail.
    </p>
@endsection
