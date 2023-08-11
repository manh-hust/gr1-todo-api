<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class PlaceStatus extends Enum
{
    const PENDING = 0;
    const ACTIVE = 1;
    const INACTIVE = 2;
}
