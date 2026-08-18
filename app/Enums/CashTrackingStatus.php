<?php

namespace App\Enums;

interface CashTrackingStatus
{
    const PENDING = 1;

    const COLLECTED = 2;

    const HANDED_OVER = 3;

    const RECONCILED = 4;

    const ANOMALY = 5;
}
