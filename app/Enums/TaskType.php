<?php

namespace App\Enums;

enum TaskType: string
{
    case BUG        = 'Bug';
    case FEATURE    = 'Feature';
    case IMPOVEMENT = 'Improvement';
}
