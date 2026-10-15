{{--
    Plain text counterpart of emails.default. Text views echo with {!! !!}: a text part is never
    read as HTML, and {{ }} would show entities such as &#039; and &amp; to the reader.
--}}
@yield('content')

--
Meer mens. Omdat we reizen.

omdatwereizen.nl · {!! config('contact.mail') !!}
Aangesloten bij STO Garant en VvKR
© {!! date('Y') !!} {!! config('app.name') !!} · KvK {!! config('contact.kvk', '97092363') !!}
@hasSection('footer')

@yield('footer')
@endif
