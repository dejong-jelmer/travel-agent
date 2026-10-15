{{--
    Opening block for internal notifications sent to the admin.
    Expects: $label (string, e.g. "Nieuwe boeking"), $title (string).
    Optional: $meta (string, e.g. a timestamp), $tone ('neutral' | 'warning' | 'danger').
--}}
@php
    $dotColors = ['neutral' => '#afcb98', 'warning' => '#f59e0b', 'danger' => '#b0413e'];
    $dotColor = $dotColors[$tone ?? 'neutral'] ?? '#afcb98';
@endphp
<p style="margin:0 0 10px 0;font-size:13px;line-height:1.5;color:#4d6f80;">
    <span style="color:{{ $dotColor }};">&#9679;</span>&nbsp; {{ $label }}
</p>

<h1 style="margin:0 0 8px 0;font-family:Georgia,'Times New Roman',serif;font-size:26px;line-height:1.3;font-weight:normal;color:#1e2d3d;">
    {{ $title }}
</h1>

@if (!empty($meta))
    <p style="margin:0;font-size:13px;line-height:1.5;color:#4d6f80;">
        {{ $meta }}
    </p>
@endif
