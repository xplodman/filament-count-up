<?php

namespace Xplodman\CountUp;

use Illuminate\Contracts\View\View;

class CountUpStat
{
    /**
     * Render an animated count-up number.
     *
     * The returned view is `Htmlable`, so it can be echoed directly in Blade
     * (`{{ CountUpStat::make(1234.5) }}`) or passed straight into a Filament
     * `Stat::make()` value (`Stat::make('Total', CountUpStat::make(1234.5))`),
     * since Filament stats render their value through the same escaping
     * mechanism that respects `Htmlable`.
     */
    public function make(
        int | float | string | null $value,
        ?int $decimals = null,
        ?int $duration = null,
        ?string $thousandsSeparator = null,
        ?string $decimalSeparator = null,
        string $prefix = '',
        string $suffix = '',
    ): View {
        return view('filament-count-up::count-up', [
            'value' => $value,
            'decimals' => $decimals,
            'duration' => $duration,
            'thousandsSeparator' => $thousandsSeparator,
            'decimalSeparator' => $decimalSeparator,
            'prefix' => $prefix,
            'suffix' => $suffix,
        ]);
    }
}
