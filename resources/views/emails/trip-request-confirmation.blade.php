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
@endphp

@section('preheader')
    {{ __('trip_request.mail.confirmation_intro', ['trip' => $tripRequest->trip->name]) }}
@endsection

@section('content')
    {{-- Greeting --}}
    <p style="margin:0 0 20px 0;font-size:15px;line-height:1.7;color:#1e2d3d;">
        {{ __('trip_request.mail.confirmation_greeting', ['name' => $tripRequest->name]) }}
    </p>

    <h1 style="margin:0 0 16px 0;font-family:Georgia,'Times New Roman',serif;font-size:26px;line-height:1.3;font-weight:normal;color:#1e2d3d;">
        {{ __('trip_request.mail.confirmation_title') }}
    </h1>

    <p style="margin:0 0 16px 0;font-size:15px;line-height:1.7;color:#1e2d3d;">
        {{ __('trip_request.mail.confirmation_intro', ['trip' => $tripRequest->trip->name]) }}
    </p>

    {{-- Summary of the submitted request --}}
    @include('emails.partials.heading', ['text' => __('trip_request.mail.confirmation_summary_header')])

    <p style="margin:0 0 8px 0;font-size:14px;line-height:1.7;color:#4d6f80;">
        {{ __('trip_request.mail.confirmation_summary_intro') }}
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
        @include('emails.partials.detail-row', [
            'label' => __('trip_request.mail.notification_label_trip'),
            'value' => $tripRequest->trip->name,
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
        @if ($tripRequest->phone)
            @include('emails.partials.detail-row', [
                'label' => __('trip_request.mail.notification_label_phone'),
                'value' => $tripRequest->phone,
            ])
        @endif
        @if ($tripRequest->notes)
            @include('emails.partials.detail-row', [
                'label' => __('trip_request.mail.notification_section_notes'),
                'value' => $tripRequest->notes,
                'multiline' => true,
            ])
        @endif
    </table>

    {{-- What to expect from the conversation --}}
    @include('emails.partials.heading', ['text' => __('trip_request.mail.confirmation_what_to_expect_header')])

    @include('emails.partials.steps', ['steps' => [
        __('trip_request.mail.confirmation_what_to_expect_1'),
        __('trip_request.mail.confirmation_what_to_expect_2'),
        __('trip_request.mail.confirmation_what_to_expect_3'),
    ]])

    {{-- Contact --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:16px 0 0 0;border-left:3px solid #afcb98;">
        <tr>
            <td style="padding:4px 0 4px 20px;font-size:14px;line-height:1.7;color:#1e2d3d;">
                {{ __('trip_request.mail.confirmation_question') }}<br>
                <a href="tel:{{ config('contact.phone') }}" style="color:#2d5f6e;text-decoration:none;">{{ config('contact.phone') }}</a>
                &nbsp;&middot;&nbsp;
                <a href="mailto:{{ config('contact.mail') }}" style="color:#2d5f6e;text-decoration:none;">{{ config('contact.mail') }}</a>
            </td>
        </tr>
    </table>

    {{-- Closing --}}
    <p style="margin:36px 0 0 0;font-size:15px;line-height:1.7;color:#1e2d3d;">
        {{ __('trip_request.mail.confirmation_closing') }}
    </p>
    <p style="margin:4px 0 0 0;font-size:15px;line-height:1.5;color:#1e2d3d;">
        <span style="font-family:Georgia,'Times New Roman',serif;font-size:18px;">{{ config('contact.first_name') }}</span><br>
        <span style="font-size:13px;color:#4d6f80;">{{ config('app.name') }}</span>
    </p>
@endsection
