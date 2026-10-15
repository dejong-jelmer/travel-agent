@extends('emails.default')

@section('preheader')
    Boeking mislukt voor {{ $bookingDetails['trip_name'] }}. Actie nodig.
@endsection

@section('content')
    @include('emails.partials.admin-intro', [
        'label' => 'Boeking mislukt',
        'title' => 'Er ging iets mis bij een boeking',
        'meta' => 'Ontvangen op ' . now()->setTimezone('Europe/Amsterdam')->format('d-m-Y \o\m H:i') . ' uur',
        'tone' => 'danger',
    ])

    {{-- Error details --}}
    @include('emails.partials.heading', ['text' => 'Foutdetails'])

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
        @include('emails.partials.detail-row', [
            'label' => 'Foutmelding',
            'value' => $errorContext,
            'tone' => 'danger',
            'strong' => true,
        ])
    </table>

    <p style="margin:20px 0 8px 0;font-size:13px;line-height:1.5;color:#4d6f80;">
        Technische details
    </p>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f5f0e8;">
        <tr>
            <td style="padding:16px 18px;font-family:Menlo,Consolas,'Courier New',monospace;font-size:12px;line-height:1.6;color:#1e2d3d;word-break:break-word;white-space:pre-wrap;">{{ $errorMessage }}</td>
        </tr>
    </table>

    {{-- Attempted booking --}}
    @include('emails.partials.heading', ['text' => 'Geprobeerde boeking'])

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
        @include('emails.partials.detail-row', [
            'label' => 'Reis',
            'value' => $bookingDetails['trip_name'],
            'strong' => true,
        ])
        @include('emails.partials.detail-row', [
            'label' => 'Vertrekdatum',
            'value' => $bookingDetails['date'],
        ])
        @if ($bookingDetails['email'])
            @include('emails.partials.detail-row', [
                'label' => 'E-mail klant',
                'value' => $bookingDetails['email'],
                'href' => 'mailto:' . $bookingDetails['email'],
            ])
        @endif
    </table>

    {{-- Next steps --}}
    @include('emails.partials.heading', ['text' => 'Wat nu?'])

    @include('emails.partials.steps', ['steps' => [
        ['Controleer de serverlogs', 'Daar vind je de volledige stacktrace.'],
        ['Neem contact op met de klant', 'Verwerk de boeking zo nodig handmatig in het admin panel.'],
        ['Zoek de oorzaak', 'Zodat deze fout niet nog een keer optreedt.'],
    ]])
@endsection

@section('footer')
    <p style="margin:0;font-size:12px;line-height:1.7;color:#4d6f80;">
        Automatische foutmelding van {{ config('app.name') }}.
    </p>
@endsection
