<?php

namespace App\Enums;

enum ModelAction: string
{
    case Stored = 'stored';
    case Updated = 'updated';
    case Deleted = 'deleted';
}
