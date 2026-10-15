{{--
    One label/value row inside a details table.
    Expects: $label (string), $value (string).
    Optional: $multiline (bool) keeps line breaks, $strong (bool) makes the value bold,
              $href (string) turns the value into a link, $note (string) adds a small line under the value,
              $tone ('success' | 'warning' | 'danger') colors the value with a small status dot.
    Rows stack vertically on small screens via the .email-label-col / .email-value-col classes.
--}}
@php
    $toneColors = ['success' => '#4f7a3a', 'warning' => '#a3640a', 'danger' => '#b0413e'];
    $toneColor = $toneColors[$tone ?? ''] ?? null;
    $valueStyle = 'color:' . ($toneColor ?? '#1e2d3d') . ';'
        . (!empty($strong) ? 'font-weight:bold;' : '')
        . (!empty($multiline) ? 'white-space:pre-line;' : '');
@endphp
<tr>
    <td class="email-label-col" width="38%"
        style="padding:14px 16px 12px 0;border-bottom:1px solid #eee8dc;vertical-align:top;font-size:13px;line-height:1.5;color:#4d6f80;">
        {{ $label }}
    </td>
    <td class="email-value-col" width="62%"
        style="padding:12px 0;border-bottom:1px solid #eee8dc;vertical-align:top;font-size:15px;line-height:1.5;color:#1e2d3d;">
        @if ($toneColor)
            <span style="color:{{ $toneColor }};">&#9679;</span>&nbsp;
        @endif
        @if (!empty($href))
            <a href="{{ $href }}" style="text-decoration:none;{{ $valueStyle }}color:#2d5f6e;">{{ $value }}</a>
        @else
            <span style="{{ $valueStyle }}">{{ $value }}</span>
        @endif
        @if (!empty($note))
            <br><span style="font-size:13px;color:#4d6f80;">{{ $note }}</span>
        @endif
    </td>
</tr>
