@extends('emails.default')

@php
    $notSpecified = __('trip_request.mail.notification_not_specified');

    if ($tripRequest->travelers_count === 9) {
        $travelers = __('trip_request.mail.notification_travelers_more_than_8');
    } elseif ($tripRequest->travelers_count) {
        $travelers = (string) $tripRequest->travelers_count;
    } else {
        $travelers = $notSpecified;
    }

    $replySubject = 'Re: ' . __('trip_request.mail.notification_reply_subject', ['trip' => $tripRequest->trip->name]);
    $replyUrl = 'mailto:' . $tripRequest->email . '?subject=' . rawurlencode($replySubject);
@endphp

@section('preheader')
    {{ __('trip_request.mail.notification_subheader', ['trip' => $tripRequest->trip->name]) }}
@endsection

@section('content')
    @include('emails.partials.admin-intro', [
        'label' => __('trip_request.mail.notification_header'),
        'title' => $tripRequest->name . ' · ' . $tripRequest->trip->name,
        'meta' => __('trip_request.mail.notification_received_at') . ' '
            . $tripRequest->created_at->setTimezone('Europe/Amsterdam')->format('d-m-Y \o\m H:i') . ' uur',
    ])

    {{-- Contact details --}}
    @include('emails.partials.heading', ['text' => __('trip_request.mail.notification_section_contact')])

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
        @include('emails.partials.detail-row', [
            'label' => __('trip_request.mail.notification_label_name'),
            'value' => $tripRequest->name,
            'strong' => true,
        ])
        @include('emails.partials.detail-row', [
            'label' => __('trip_request.mail.notification_label_email'),
            'value' => $tripRequest->email,
            'href' => 'mailto:' . $tripRequest->email,
        ])
        @if ($tripRequest->phone)
            @include('emails.partials.detail-row', [
                'label' => __('trip_request.mail.notification_label_phone'),
                'value' => $tripRequest->phone,
                'href' => 'tel:' . $tripRequest->phone,
            ])
        @endif
    </table>

    {{-- Trip and preferences --}}
    @include('emails.partials.heading', ['text' => __('trip_request.mail.notification_section_preferences')])

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
        @include('emails.partials.detail-row', [
            'label' => __('trip_request.mail.notification_label_trip'),
            'value' => $tripRequest->trip->name,
            'note' => $tripRequest->trip->slug,
            'strong' => true,
        ])
        @include('emails.partials.detail-row', [
            'label' => __('trip_request.mail.notification_label_period'),
            'value' => $tripRequest->preferred_month ?? $notSpecified,
        ])
        @if ($tripRequest->preferred_period_note)
            @include('emails.partials.detail-row', [
                'label' => __('trip_request.mail.notification_label_period_note'),
                'value' => $tripRequest->preferred_period_note,
            ])
        @endif
        @include('emails.partials.detail-row', [
            'label' => __('trip_request.mail.notification_label_travelers'),
            'value' => $travelers,
        ])
        @include('emails.partials.detail-row', [
            'label' => __('trip_request.mail.notification_label_station'),
            'value' => $tripRequest->departure_station ?? $notSpecified,
        ])
    </table>

    {{-- Notes --}}
    @include('emails.partials.heading', ['text' => __('trip_request.mail.notification_section_notes')])

    @if ($tripRequest->notes)
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-left:3px solid #afcb98;">
            <tr>
                <td style="padding:4px 0 4px 20px;font-size:15px;line-height:1.7;color:#1e2d3d;white-space:pre-wrap;">{{ $tripRequest->notes }}</td>
            </tr>
        </table>
    @else
        <p style="margin:0;font-size:14px;line-height:1.7;color:#4d6f80;">
            {{ __('trip_request.mail.notification_no_notes') }}
        </p>
    @endif

    {{-- Quick reply --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center" style="padding:36px 0 0 0;">
                @include('emails.partials.button', ['url' => $replyUrl, 'text' => __('trip_request.mail.notification_reply_button')])
            </td>
        </tr>
    </table>
@endsection

@section('footer')
    <p style="margin:0;font-size:12px;line-height:1.7;color:#4d6f80;">
        Automatische notificatie van een reisaanvraag. Reageer rechtstreeks naar de afzender, niet op deze e-mail.
    </p>
@endsection
