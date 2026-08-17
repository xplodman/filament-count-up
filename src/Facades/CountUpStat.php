<?php

namespace Xplodman\CountUp\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Xplodman\CountUp\CountUpStat
 */
class CountUpStat extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Xplodman\CountUp\CountUpStat::class;
    }
}
