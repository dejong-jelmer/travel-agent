{{--
    Plain text counterpart of emails.partials.admin-intro.
    Expects: $label (string), $title (string). Optional: $meta (string).
--}}
{!! $label !!}
{!! $title !!}
@if (!empty($meta))
{!! $meta !!}
@endif
