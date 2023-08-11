<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class TaskStatus extends Enum
{
    const TODO = 0;
    const INPROGRESS = 1;
    const DONE = 2;
}
