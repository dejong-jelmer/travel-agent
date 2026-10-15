@extends('emails.text.default')
@php
    // Campaign content from the editor as plain text: links keep their url, list items get a dash
    // and block elements end with a blank line.
    $content = preg_replace_callback(
        '/<a\s[^>]*href="([^"]*)"[^>]*>(.*?)<\/a>/is',
        fn (array $link) => strip_tags($link[2]) === $link[1] ? $link[1] : $link[2] . ' (' . $link[1] . ')',
        (string) $campaign->content,
    );
    $content = preg_replace(
        ['/<\/p>\s*<\/li>/i', '/<li[^>]*>\s*(<p[^>]*>)?/i', '/<\/li>/i', '/<br\s*\/?>/i', '/<hr[^>]*>/i', '/<\/(p|h[1-6]|blockquote|pre|ul|ol)>/i'],
        ['</li>', '- ', "\n", "\n", "---\n\n", "\n\n"],
        $content,
    );
    $content = trim(preg_replace(['/^[ \t]+|[ \t]+$/m', "/\n{3,}/"], ['', "\n\n"], html_entity_decode(strip_tags($content))));
@endphp
@section('content')
{!! $campaign->subject !!}

{!! $content !!}
@if (!empty($featuredTrips) && count($featuredTrips) > 0)

== {!! count($featuredTrips) === 1 ? 'De reis' : 'De reizen' !!} ==
@foreach ($featuredTrips as $trip)

{!! $trip->name !!}
{!! Str::limit($trip->description_plain, 160) !!}
Bekijk reis: {!! route('trips.show', $trip->slug) !!}
@endforeach
@endif
@if (!empty($ctaText) && !empty($ctaUrl))

{!! $ctaText !!}:
{!! $ctaUrl !!}
@endif

{!! config('contact.first_name') !!}
{!! config('app.name') !!}
@endsection
@section('footer')
@if (!empty($socialLinks))
@foreach ($socialLinks as $platform => $url)
{!! ucfirst($platform) !!}: {!! $url !!}
@endforeach

@endif
Je ontvangt deze e-mail omdat je je hebt ingeschreven voor de nieuwsbrief.
Uitschrijven: {!! $unsubscribeUrl ?? route('newsletter.subscription.unsubscribe', $subscriber->unsubscribe_token ?? '') !!}
@endsection
