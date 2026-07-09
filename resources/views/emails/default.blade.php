<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? config('app.name') }}</title>
    <style>
        @media only screen and (max-width: 620px) {
            .email-wrapper {
                width: 100% !important;
                margin: 0 auto !important;
                border-radius: 0 !important;
            }
            .email-content {
                padding: 20px !important;
            }
            .email-label-col,
            .email-value-col {
                display: block !important;
                width: 100% !important;
            }
            .email-label-col {
                padding-top: 12px !important;
                padding-bottom: 2px !important;
            }
            .email-value-col {
                padding-top: 0 !important;
            }
        }
    </style>
</head>

<body style="background:#fbfbf7;margin:0;font-family:Arial,sans-serif;color:#2d5f6e;">

    <table align="center" width="100%" cellpadding="0" cellspacing="0" class="email-wrapper"
        style="max-width:600px;margin:30px auto;background:#ffffff;border-radius:8px;overflow:hidden;">
        <tr>
            <td align="left" style="text-align:left; background:#ffffff;padding:20px;">
                {{-- embed via $message if available --}}
                @if (isset($message) && file_exists(resource_path('images/logos/mail/logo-text.png')))
                    <img src="{{ $message->embed(resource_path('images/logos/mail/logo-text.png')) }}"
                        alt="{{ config('app.name') }} - duurzame treinreizen door Europa"
                        style="max-height:100px;display:block;margin:0 auto;">
                @elseif (file_exists(public_path('images/logos/mail/logo-text.png')))
                    {{-- fallback to public copy --}}
                    <img src="{{ asset('images/logos/mail/logo-text.png') }}"
                        alt="{{ config('app.name') }} - duurzame treinreizen door Europa"
                        style="max-height:100px;display:block;margin:0 auto;">
                @else
                    {{-- simple text fallback --}}
                    <h1 style="margin:0;font-size:24px;color:#2d5f6e;">{{ config('app.name') }}</h1>
                @endif
            </td>
        </tr>

        <tr>
            <td class="email-content" style="padding:30px;">
                @yield('content')
            </td>
        </tr>

        <tr>
            <td align="center" style="background:#ffffff;padding:20px;font-size:12px;color:#82b2ca;">
                © {{ date('Y') }} {{ config('app.name') }}. Alle rechten voorbehouden.
            </td>
        </tr>
    </table>

</body>

</html>
