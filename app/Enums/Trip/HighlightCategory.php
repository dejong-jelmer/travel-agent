<?php

namespace App\Enums\Trip;

use App\Enums\Traits\HasTranslatableLabel;
use App\Enums\Traits\Selectable;

/**
 * Keep the icon names in sync with the icon map in resources/js/Components/Atoms/HighlightIcon.vue: an icon that is
 * missing there silently renders nothing.
 */
enum HighlightCategory: string
{
    use HasTranslatableLabel,
        Selectable;

    case Roman = 'roman';
    case Church = 'church';
    case Castle = 'castle';
    case HistoricVillage = 'historic_village';
    case HistoricCity = 'historic_city';
    case Museum = 'museum';
    case Theater = 'theater';
    case Viewpoint = 'viewpoint';
    case Square = 'square';
    case Walk = 'walk';
    case Mountain = 'mountain';
    case Garden = 'garden';
    case Water = 'water';
    case Food = 'food';
    case Wine = 'wine';
    case Prehistory = 'prehistory';
    case Cave = 'cave';
    case DayTrip = 'day_trip';
    case Other = 'other';

    /**
     * The name of the Lucide icon, or of an own icon component for the categories Lucide has no fitting icon for.
     */
    public function icon(): string
    {
        return match ($this) {
            self::Roman => 'Landmark',
            self::Church => 'Church',
            self::Castle => 'Castle',
            self::HistoricVillage => 'HistoricTown',
            self::HistoricCity => 'HistoricCity',
            self::Museum => 'Palette',
            self::Theater => 'Drama',
            self::Viewpoint => 'Binoculars',
            self::Square => 'Store',
            self::Walk => 'Footprints',
            self::Mountain => 'Mountain',
            self::Garden => 'Flower2',
            self::Water => 'Waves',
            self::Food => 'UtensilsCrossed',
            self::Wine => 'Wine',
            self::Prehistory => 'LascauxHorse',
            self::Cave => 'Cave',
            self::DayTrip => 'Signpost',
            self::Other => 'MapPin',
        };
    }

    /**
     * Added to the select options, so the admin can preview the icon of every category.
     *
     * @return array{icon: string}
     */
    public function extraOptions(): array
    {
        return ['icon' => $this->icon()];
    }

    protected function getLabelKey(): string
    {
        return 'trip.highlight_category';
    }
}
