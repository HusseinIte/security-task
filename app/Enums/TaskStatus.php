<?php

namespace App\Enums;

enum TaskStatus: string
{
    case OPEN        = 'Open';
    case IN_PROGRESS = 'In Progress';
    case COMPLETED   = 'Completed';
    case BLOCKED     = 'Blocked';
}
