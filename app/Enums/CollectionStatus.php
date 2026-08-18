<?php

namespace App\Enums;

interface CollectionStatus
{
    const PENDING_ASSIGNMENT = 1;

    const ASSIGNED = 2;

    const PICKING_UP = 3;

    const COLLECTED = 4;

    const COMPLETED = 5;

    const CANCELLED = 6;
}
