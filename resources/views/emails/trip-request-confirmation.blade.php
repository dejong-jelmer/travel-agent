@extends('emails.default')


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

    {{-- A request on a departure reserves nothing yet --}}
    @if ($tripRequest->departure)
        <p style="margin:0 0 16px 0;font-size:15px;line-height:1.7;color:#1e2d3d;">
            {{ __($tripRequest->interest_only ? 'trip_request.mail.confirmation_interest_note' : 'trip_request.mail.confirmation_group_note') }}
        </p>
    @endif

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
        @foreach ($tripRequest->summaryRows() as $row)
            @include('emails.partials.detail-row', $row)
        @endforeach
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

    @php($expect = $tripRequest->departure ? 'confirmation_what_to_expect_group_' : 'confirmation_what_to_expect_')
    @include('emails.partials.steps', ['steps' => [
        __("trip_request.mail.{$expect}1"),
        __("trip_request.mail.{$expect}2"),
        __("trip_request.mail.{$expect}3"),
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
