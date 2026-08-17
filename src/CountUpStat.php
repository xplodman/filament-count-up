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
     *
     * By default, a fresh `wire:key` is generated on every render, so that
     * inside a Livewire component that polls or gets manually refreshed
     * (e.g. a `wire:click="$refresh"` button), the animation always replays
     * with the latest value. Livewire morphs the DOM in place on a
     * re-render, and Alpine deliberately preserves already-initialized
     * `x-data` components during a morph - so without a changing `wire:key`,
     * the span would otherwise keep showing its old animated value forever
     * instead of re-counting. Pass a string for a stable custom key, or
     * `false` to opt out and let Alpine/Livewire's normal morph behaviour
     * apply (the value then only updates if the element is recreated for
     * some other reason).
     */
    public function make(
        int | float | string | null $value,
        ?int $decimals = null,
        ?int $duration = null,
        ?string $thousandsSeparator = null,
        ?string $decimalSeparator = null,
        string $prefix = '',
        string $suffix = '',
        bool | string $wireKey = true,
    ): View {
        return view('filament-count-up::count-up', [
            'value' => $value,
            'decimals' => $decimals,
            'duration' => $duration,
            'thousandsSeparator' => $thousandsSeparator,
            'decimalSeparator' => $decimalSeparator,
            'prefix' => $prefix,
            'suffix' => $suffix,
            'wireKey' => $wireKey,
        ]);
    }
}
