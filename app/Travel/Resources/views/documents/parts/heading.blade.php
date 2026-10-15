{{-- The heading of a topic in the Praktisch part: an icon on a tile, and the title. --}}
@use('App\Travel\Documents\Icon')
<table class="heading full">
    <tr>
        <td class="icon-tile"><div><img src="{{ Icon::src($icon) }}" alt=""></div></td>
        <td><h2 class="w8">{{ $title }}</h2></td>
    </tr>
</table>
