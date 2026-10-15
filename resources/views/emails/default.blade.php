<!DOCTYPE html>
<html lang="nl" xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>{{ $subject ?? config('app.name') }}</title>
    <style>
        body { margin: 0; padding: 0; -webkit-text-size-adjust: 100%; }
        a { color: #2d5f6e; }

        /* Rich text from the editor (newsletters): elements arrive without inline styles */
        .email-prose p { margin: 0 0 16px 0; }
        .email-prose h2 { margin: 32px 0 12px 0; font-family: Georgia, 'Times New Roman', serif; font-size: 21px; line-height: 1.3; font-weight: normal; color: #1e2d3d; }
        .email-prose h3 { margin: 28px 0 10px 0; font-family: Georgia, 'Times New Roman', serif; font-size: 18px; line-height: 1.3; font-weight: normal; color: #1e2d3d; }
        .email-prose ul, .email-prose ol { margin: 0 0 16px 0; padding-left: 22px; }
        .email-prose li { margin: 0 0 6px 0; }
        .email-prose a { color: #2d5f6e; text-decoration: underline; }
        .email-prose img { max-width: 100%; height: auto; }
        .email-prose blockquote { margin: 24px 0; padding: 4px 0 4px 20px; border-left: 3px solid #afcb98; font-family: Georgia, 'Times New Roman', serif; font-style: italic; color: #2d5f6e; }
        .email-prose hr { border: 0; border-top: 1px solid #e6dfd2; margin: 32px 0; }

        @media only screen and (max-width: 620px) {
            .email-outer { padding: 0 !important; }
            .email-wrapper { width: 100% !important; }
            .email-content { padding: 32px 24px !important; }
            .email-header { padding: 28px 24px 0 24px !important; }
            .email-footer { padding: 28px 24px !important; }
            .email-label-col,
            .email-value-col {
                display: block !important;
                width: 100% !important;
            }
            .email-label-col { padding: 14px 0 2px 0 !important; border-bottom: 0 !important; }
            .email-value-col { padding: 0 0 14px 0 !important; }
            .email-stack { display: block !important; width: 100% !important; }
            .email-stack-img { padding: 0 0 16px 0 !important; }
            .email-stack-img img { width: 100% !important; max-width: 100% !important; height: auto !important; }
        }
    </style>
</head>

<body style="margin:0;padding:0;background:#f5f0e8;font-family:Helvetica,Arial,sans-serif;color:#1e2d3d;">

    {{-- Hidden preview text shown in the inbox list --}}
    @hasSection('preheader')
        <div style="display:none;max-height:0;overflow:hidden;mso-hide:all;font-size:1px;line-height:1px;color:#f5f0e8;">
            @yield('preheader')
        </div>
    @endif

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f5f0e8;">
        <tr>
            <td align="center" class="email-outer" style="padding:40px 16px;">

                <!--[if mso]><table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0"><tr><td><![endif]-->
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" class="email-wrapper"
                    style="max-width:600px;background:#ffffff;border-top:3px solid #2d5f6e;">

                    {{-- Header with logo --}}
                    <tr>
                        <td align="center" class="email-header" style="padding:36px 48px 0 48px;">
                            @if (isset($message) && file_exists(resource_path('images/logos/mail/logo-text.png')))
                                <img src="{{ $message->embed(resource_path('images/logos/mail/logo-text.png')) }}"
                                    alt="{{ config('app.name') }}" width="140"
                                    style="display:block;width:140px;max-width:140px;height:auto;border:0;">
                            @elseif (file_exists(public_path('images/logos/mail/logo-text.png')))
                                <img src="{{ asset('images/logos/mail/logo-text.png') }}"
                                    alt="{{ config('app.name') }}" width="140"
                                    style="display:block;width:140px;max-width:140px;height:auto;border:0;">
                            @else
                                <p style="margin:0;font-family:Georgia,'Times New Roman',serif;font-size:22px;color:#2d5f6e;">
                                    {{ config('app.name') }}
                                </p>
                            @endif
                        </td>
                    </tr>

                    {{-- Main content --}}
                    <tr>
                        <td class="email-content" style="padding:40px 48px 48px 48px;font-size:15px;line-height:1.7;color:#1e2d3d;">
                            @yield('content')
                        </td>
                    </tr>
                </table>

                {{-- Footer --}}
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" class="email-wrapper"
                    style="max-width:600px;">
                    <tr>
                        <td align="center" class="email-footer"
                            style="padding:32px 48px;font-size:12px;line-height:1.7;color:#4d6f80;">
                            <p style="margin:0 0 12px 0;font-family:Georgia,'Times New Roman',serif;font-style:italic;font-size:14px;color:#2d5f6e;">
                                Meer mens. Omdat we reizen.
                            </p>
                            <p style="margin:0 0 4px 0;">
                                <a href="{{ config('app.url') }}" style="color:#4d6f80;text-decoration:underline;">omdatwereizen.nl</a>
                                &nbsp;&middot;&nbsp;
                                <a href="mailto:{{ config('contact.mail') }}" style="color:#4d6f80;text-decoration:underline;">{{ config('contact.mail') }}</a>
                            </p>
                            <p style="margin:0 0 4px 0;">
                                Aangesloten bij STO Garant en VvKR
                            </p>
                            <p style="margin:0;color:#4d6f80;">
                                &copy; {{ date('Y') }} {{ config('app.name') }} &middot; KvK {{ config('contact.kvk', '97092363') }}
                            </p>
                            @hasSection('footer')
                                <div style="margin:16px 0 0 0;padding:16px 0 0 0;border-top:1px solid #e6dfd2;">
                                    @yield('footer')
                                </div>
                            @endif
                        </td>
                    </tr>
                </table>
                <!--[if mso]></td></tr></table><![endif]-->

            </td>
        </tr>
    </table>

</body>

</html>
