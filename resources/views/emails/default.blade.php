<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="utf-8">
    <title>{{ $subject ?? config('app.name') }}</title>
</head>

<body style="background:#FAFAFA;margin:0;font-family:Arial,sans-serif;color:#1B3A4B;">

    <table align="center" width="600" cellpadding="0" cellspacing="0"
        style="margin:30px auto;background:#FAFAFA;border-radius:8px;overflow:hidden;">
        <tr>
            <td align="center" style="text-align:left; background:#FFFFFF;padding:20px;border-bottom:3px solid #AFCB98;">
                {{-- embed via $message als beschikbaar (robust) --}}
                @if (isset($message) && file_exists(resource_path('images/logo.png')))
                    <img src="{{ $message->embed(resource_path('images/logo.png')) }}"
                        alt="{{ config('app.name') }}" style="max-height:60px;display:block;margin:0 auto;">
                @elseif (file_exists(public_path('images/logo.png')))
                    {{-- fallback naar public copy --}}
                    <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}"
                        style="max-height:60px;display:block;margin:0 auto;">
                @else
                    {{-- eenvoudige tekst fallback --}}
                    <h1 style="margin:0;font-size:24px;color:#1B3A4B;">{{ config('app.name') }}</h1>
                @endif
            </td>
        </tr>

        <tr>
            <td style="padding:30px;">
                @yield('content')
            </td>
        </tr>

        <tr>
            <td align="center" style="background:#F2F4F3;padding:20px;font-size:12px;color:#B17C65;">
                © {{ date('Y') }} {{ config('app.name') }}. Alle rechten voorbehouden.
            </td>
        </tr>
    </table>

</body>

</html>
