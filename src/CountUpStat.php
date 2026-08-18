<?php

namespace Xplodman\CountUp;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\HtmlString;

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

    /**
     * Auto-animate any Filament `Stat::make()` value without callers having
     * to opt in per-widget. Used by the overriding
     * `filament-widgets::stats-overview-widget.stat` view that
     * {@see CountUpPlugin} registers on a panel.
     *
     * - `Htmlable` values (including ones already built with `make()`) and
     *   `null` pass through untouched.
     * - Plain `int`/`float` values are wrapped whole.
     * - Strings are scanned for numeric tokens (`1,250,000`, `4,320.00`,
     *   `23`, ...); each one is replaced with an animated count-up span and
     *   everything else (currency codes, `%`, `/`, `ms`, ...) is kept as
     *   escaped literal text around it. This lets existing
     *   `number_format()`-built strings, ratios like "12 / 340", and
     *   suffixed values like "180ms" all animate with zero changes to the
     *   widget code that built them.
     * - Strings with no numeric token at all (e.g. a placeholder dash) are
     *   returned escaped, matching Blade's default `{{ }}` behaviour.
     */
    public function animate(mixed $value): Htmlable
    {
        if ($value instanceof Htmlable) {
            return $value;
        }

        if ($value === null) {
            return new HtmlString('');
        }

        $defaults = $this->activePluginDefaults();

        if (is_int($value) || is_float($value)) {
            return new HtmlString($this->make(
                $value,
                decimals: $defaults['decimals'],
                duration: $defaults['duration'],
                thousandsSeparator: $defaults['thousandsSeparator'],
                decimalSeparator: $defaults['decimalSeparator'],
            )->render());
        }

        if (! is_string($value)) {
            return new HtmlString(e((string) $value));
        }

        return new HtmlString($this->animateNumericTokens($value, $defaults));
    }

    /**
     * @param  array{decimals: ?int, duration: ?int, thousandsSeparator: ?string, decimalSeparator: ?string}  $defaults
     */
    protected function animateNumericTokens(string $value, array $defaults): string
    {
        $pattern = '/-?\d{1,3}(?:,\d{3})+(?:\.\d+)?|-?\d+(?:\.\d+)?/';

        if (! preg_match_all($pattern, $value, $matches, PREG_OFFSET_CAPTURE)) {
            return e($value);
        }

        $html = '';
        $cursor = 0;

        foreach ($matches[0] as [$token, $offset]) {
            $html .= e(substr($value, $cursor, $offset - $cursor));

            $tokenDecimals = str_contains($token, '.') ? strlen(explode('.', $token)[1]) : null;
            $number = (float) str_replace(',', '', $token);

            $html .= $this->make(
                $number,
                decimals: $defaults['decimals'] ?? $tokenDecimals,
                duration: $defaults['duration'],
                thousandsSeparator: $defaults['thousandsSeparator'],
                decimalSeparator: $defaults['decimalSeparator'],
            )->render();

            $cursor = $offset + strlen($token);
        }

        $html .= e(substr($value, $cursor));

        return $html;
    }

    /**
     * Configuration set fluently on the panel's registered
     * {@see CountUpPlugin} (e.g. `CountUpPlugin::make()->decimals(2)`), if
     * any. Falls back to all-`null` (which defers to `count-up` config
     * values) when no panel/plugin context is available - e.g. outside an
     * HTTP request, or when the plugin isn't registered on the panel.
     *
     * @return array{decimals: ?int, duration: ?int, thousandsSeparator: ?string, decimalSeparator: ?string}
     */
    protected function activePluginDefaults(): array
    {
        $fallback = [
            'decimals' => null,
            'duration' => null,
            'thousandsSeparator' => null,
            'decimalSeparator' => null,
        ];

        try {
            $plugin = CountUpPlugin::get();
        } catch (\Throwable) {
            return $fallback;
        }

        return [
            'decimals' => $plugin->getDecimals(),
            'duration' => $plugin->getDuration(),
            'thousandsSeparator' => $plugin->getThousandsSeparator(),
            'decimalSeparator' => $plugin->getDecimalSeparator(),
        ];
    }
}
