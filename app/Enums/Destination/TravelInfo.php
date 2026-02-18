<?php

namespace App\Enums\Destination;

use App\Enums\Traits\HasTranslatableLabel;

enum TravelInfo: string
{
    use HasTranslatableLabel;

    case GeneralInfo = 'general_info';
    case TravelDocuments = 'travel_documents';
    case Visa = 'visa';
    case HealthSafety = 'health_safety';
    case Currency = 'currency';
    case Climate = 'climate';

    protected function getLabelKey(): string
    {
        return 'destination.travel-info.sections';
    }
}
