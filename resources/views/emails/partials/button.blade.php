{{--
    Bulletproof button that also renders as a solid block in Outlook.
    Expects: $url (string), $text (string), optional $align ('center' | 'left').
--}}
<table role="presentation" cellpadding="0" cellspacing="0" border="0" align="{{ $align ?? 'center' }}" style="{{ ($align ?? 'center') === 'center' ? 'margin:0 auto;' : 'margin:0;' }}">
    <tr>
        <td bgcolor="#2d5f6e" style="background:#2d5f6e;border-radius:4px;">
            <a href="{{ $url }}"
                style="display:inline-block;padding:14px 32px;font-family:Helvetica,Arial,sans-serif;font-size:15px;line-height:1.2;font-weight:bold;color:#ffffff;text-decoration:none;border-radius:4px;">
                {{ $text }}
            </a>
        </td>
    </tr>
</table>
