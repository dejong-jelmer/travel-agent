<?php

namespace App\Enums;

enum ModelAction: string
{
    case Created = 'created';
    case Updated = 'updated';
    case Destroyed = 'destroyed';
}
