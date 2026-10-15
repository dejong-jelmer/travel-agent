{{-- A link to a page elsewhere, such as the information on a line, with $label as its text. The external-link icon
    marks it, so it is not overlooked. The page opens it in a tab of its own. --}}
@use('App\Travel\Documents\Icon')
<p class="info-link"><a class="w7" href="{{ $href }}" target="_blank" rel="noopener"><img src="{{ Icon::src('external-link') }}" alt="">{{ $label }}</a></p>
