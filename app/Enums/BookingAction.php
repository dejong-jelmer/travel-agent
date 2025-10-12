<?php

namespace App\Enums;

enum BookingAction: string
{
    case Stored = 'stored';
    case Updated = 'updated';
}
