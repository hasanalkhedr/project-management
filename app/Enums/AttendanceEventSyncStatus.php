<?php

namespace App\Enums;

enum AttendanceEventSyncStatus: string
{
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
}
