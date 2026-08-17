<?php

namespace Xplodman\CountUp\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Xplodman\CountUp\CountUp
 */
class CountUp extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Xplodman\CountUp\CountUp::class;
    }
}
