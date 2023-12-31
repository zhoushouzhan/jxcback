<?php

declare(strict_types=1);

namespace yp;

use Nyg\Holiday;

class Date
{
    public function getNow()
    {
        $time = time();
        return $time;
    }
}
