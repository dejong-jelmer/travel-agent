<?php
namespace App\Enums;

use App\Enums\Traits\Selectable;

enum Transport: string
{
    use Selectable;
    case TRAIN = 'Trein';
    case BOAT = 'Boot';
    case BUS = 'Bus';
    case TRANSFER = 'Transfer';
    case AIRPLANE = 'Vliegtuig';
}
