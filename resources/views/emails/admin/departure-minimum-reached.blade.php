@extends('emails.default')

@php
    $period = $departure->departs_on->format('d-m-Y') . ' – ' . $departure->returns_on->format('d-m-Y');
@endphp

@section('preheader')
    {{ __('departure.mail.minimum_reached.intro') }}
@endsection

@section('content')
    @include('emails.partials.admin-intro', [
        'label' => __('departure.mail.minimum_reached.label'),
        'title' => $departure->trip->name . ' · ' . $departure->departs_on->format('d-m-Y'),
    ])

    <p style="margin:0 0 8px 0;font-size:15px;line-height:1.7;color:#1e2d3d;">
        {{ __('departure.mail.minimum_reached.intro') }}
    </p>
    <p style="margin:0;font-size:15px;line-height:1.7;color:#1e2d3d;">
        {{ __('departure.mail.minimum_reached.action') }}
    </p>

    @include('emails.partials.heading', ['text' => __('departure.mail.section_departure')])

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
        @include('emails.partials.detail-row', [
            'label' => __('departure.mail.label_trip'),
            'value' => $departure->trip->name,
            'strong' => true,
        ])
        @include('emails.partials.detail-row', [
            'label' => __('departure.mail.label_period'),
            'value' => $period,
        ])
        @include('emails.partials.detail-row', [
            'label' => __('departure.mail.label_confirmed'),
            'value' => __('departure.mail.confirmed_of_minimum', [
                'confirmed' => $departure->confirmedParticipants(),
                'minimum' => $departure->min_participants,
            ]),
            'tone' => 'success',
        ])
        @include('emails.partials.detail-row', [
            'label' => __('departure.mail.label_occupied'),
            'value' => __('departure.mail.occupied_of_maximum', [
                'occupied' => $departure->occupiedSeats(),
                'maximum' => $departure->max_participants,
            ]),
        ])
        @include('emails.partials.detail-row', [
            'label' => __('departure.mail.label_decision_date'),
            'value' => $departure->decision_date->format('d-m-Y'),
        ])
    </table>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center" style="padding:36px 0 0 0;">
                @include('emails.partials.button', [
                    'url' => route('admin.departures.show', $departure),
                    'text' => __('departure.mail.button'),
                ])
            </td>
        </tr>
    </table>
@endsection

@section('footer')
    <p style="margin:0;font-size:12px;line-height:1.7;color:#4d6f80;">
        {{ __('departure.mail.footer') }}
    </p>
@endsection
