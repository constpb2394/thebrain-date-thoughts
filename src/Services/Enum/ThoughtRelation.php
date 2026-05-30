<?php

namespace App\Services\Enum;

enum ThoughtRelation: int
{
    case CHILD = 1;
    case PARENT = 2;
    case JUMP = 3;
    case SIBLING = 4;
}
