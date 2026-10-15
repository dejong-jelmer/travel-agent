{{-- The footer of a part: the emergency number of each country of the trip, and the number of the agency. --}}
@use('App\Travel\Documents\Country')
@use('App\Travel\Documents\Format')
<footer class="foot">
    <div class="foot-title kicker w8 sun">In geval van nood</div>
    @forelse (Country::all($document->destinations()) as $country)
        <div class="foot-row">
            <table class="full">
                <tr>
                    <td class="foot-label">Noodnummer {{ $country->name }}</td>
                    <td class="foot-number w8 right nowrap"><a href="tel:112">{{ $country->emergencyNumbers }}</a></td>
                </tr>
            </table>
        </div>
    @empty
        <div class="foot-row">
            <table class="full">
                <tr>
                    <td class="foot-label">Noodnummer</td>
                    <td class="foot-number w8 right nowrap"><a href="tel:112">112</a></td>
                </tr>
            </table>
        </div>
    @endforelse
    @if ($document->emergencyPhone)
        <div class="foot-row foot-row-agency">
            <table class="full">
                <tr>
                    <td class="foot-label w6">{{ config('app.name') }}</td>
                    <td class="foot-number w8 right nowrap"><a href="{{ Format::phoneUrl($document->emergencyPhone) }}">{{ $document->emergencyPhone }}</a></td>
                </tr>
            </table>
        </div>
    @endif
</footer>
