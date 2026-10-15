{{--
    Numbered list of steps with outlined number circles.
    Expects: $steps (array of [title, description] pairs, or plain strings).
--}}
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
    @foreach ($steps as $stepIndex => $step)
        <tr>
            <td width="40" style="padding:0 0 18px 0;vertical-align:top;">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td align="center" width="26" height="26"
                            style="width:26px;height:26px;border:1px solid #2d5f6e;border-radius:50%;font-size:13px;line-height:26px;color:#2d5f6e;">
                            {{ $stepIndex + 1 }}
                        </td>
                    </tr>
                </table>
            </td>
            <td style="padding:2px 0 18px 0;vertical-align:top;font-size:15px;line-height:1.6;color:#1e2d3d;">
                @if (is_array($step))
                    <strong style="font-weight:bold;">{{ $step[0] }}</strong><br>
                    <span style="color:#4d6f80;">{{ $step[1] }}</span>
                @else
                    {{ $step }}
                @endif
            </td>
        </tr>
    @endforeach
</table>
