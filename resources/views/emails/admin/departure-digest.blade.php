@extends('emails.default')

@section('preheader')
    {{ __('departure.mail.digest.intro') }}
@endsection

@section('content')
    @include('emails.partials.admin-intro', [
        'label' => __('departure.mail.digest.label'),
        'title' => __('departure.mail.digest.title'),
        'tone' => 'warning',
    ])

    <p style="margin:0;font-size:15px;line-height:1.7;color:#1e2d3d;">
        {{ __('departure.mail.digest.intro') }}
    </p>

    @foreach ($groups as $key => $departures)
        @include('emails.partials.heading', ['text' => __("departure.mail.digest.groups.{$key}.title")])

        <p style="margin:0 0 12px 0;font-size:14px;line-height:1.7;color:#4d6f80;">
            {{ __("departure.mail.digest.groups.{$key}.help") }}
        </p>

        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
            @foreach ($departures as $departure)
                @include('emails.partials.detail-row', [
                    'label' => $departure->trip->name,
                    'value' => $departure->departs_on->format('d-m-Y') . ' – ' . $departure->returns_on->format('d-m-Y'),
                    'href' => route('admin.departures.show', $departure),
                    'note' => __('departure.mail.digest.row', [
                        'confirmed' => $departure->confirmedParticipants(),
                        'minimum' => $departure->min_participants,
                        'occupied' => $departure->occupiedSeats(),
                        'maximum' => $departure->max_participants,
                        'date' => $departure->decision_date->format('d-m-Y'),
                    ]),
                    'strong' => true,
                ])
            @endforeach
        </table>
    @endforeach

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center" style="padding:36px 0 0 0;">
                @include('emails.partials.button', [
                    'url' => route('admin.departures.index'),
                    'text' => __('departure.mail.digest.button'),
                ])
            </td>
        </tr>
    </table>
@endsection

@section('footer')
    <p style="margin:0;font-size:12px;line-height:1.7;color:#4d6f80;">
        {{ __('departure.mail.digest.footer') }}
    </p>
@endsection
