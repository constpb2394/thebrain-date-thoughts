<?php

namespace App\Services\Enum;

enum ThoughtKind: int
{
    case NORMAL = 1;
    case TYPE = 2;
    case EVENT = 3;
    case TAG = 4;
    case SYSTEM = 5;
}
