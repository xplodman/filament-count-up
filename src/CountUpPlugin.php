<?php

namespace Xplodman\CountUp;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Support\Facades\View;
use Illuminate\View\FileViewFinder;

class CountUpPlugin implements Plugin
{
    protected bool $autoAnimateStats = true;

    protected ?int $decimals = null;

    protected ?int $duration = null;

    protected ?string $thousandsSeparator = null;

    protected ?string $decimalSeparator = null;

    public function getId(): string
    {
        return 'filament-count-up';
    }

    public function register(Panel $panel): void
    {
        //
    }

    /**
     * Once this plugin is registered on a panel, every
     * `Filament\Widgets\StatsOverviewWidget\Stat::make()` value on that
     * panel animates automatically - no per-widget `CountUpStat::make()`
     * call needed. This works by overriding the package-provided
     * `filament-widgets::stats-overview-widget.stat` view with one that
     * routes the value through {@see CountUpStat::animate()} instead of
     * printing it directly. Disable with `->autoAnimateStats(false)`.
     */
    public function boot(Panel $panel): void
    {
        if ($this->autoAnimateStats) {
            $this->registerStatOverrideView();
        }
    }

    /**
     * Toggle the automatic `Stat::make()` animation registered in `boot()`.
     * Pass `false` to opt out and only animate values explicitly built with
     * `CountUpStat::make()`.
     */
    public function autoAnimateStats(bool $condition = true): static
    {
        $this->autoAnimateStats = $condition;

        return $this;
    }

    /**
     * Default decimal places used when auto-animating a `Stat::make()`
     * value that doesn't already carry its own decimal precision (e.g. a
     * plain integer, or a string with no decimal point). `null` defers to
     * the `count-up.decimals` config value.
     */
    public function decimals(?int $decimals): static
    {
        $this->decimals = $decimals;

        return $this;
    }

    /**
     * Default animation duration (ms) for auto-animated `Stat::make()`
     * values. `null` defers to the `count-up.duration` config value.
     */
    public function duration(?int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Default thousands separator for auto-animated `Stat::make()` values.
     * `null` defers to the `count-up.thousands_separator` config value.
     */
    public function thousandsSeparator(?string $separator): static
    {
        $this->thousandsSeparator = $separator;

        return $this;
    }

    /**
     * Default decimal separator for auto-animated `Stat::make()` values.
     * `null` defers to the `count-up.decimal_separator` config value.
     */
    public function decimalSeparator(?string $separator): static
    {
        $this->decimalSeparator = $separator;

        return $this;
    }

    public function isAutoAnimatingStats(): bool
    {
        return $this->autoAnimateStats;
    }

    public function getDecimals(): ?int
    {
        return $this->decimals;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function getThousandsSeparator(): ?string
    {
        return $this->thousandsSeparator;
    }

    public function getDecimalSeparator(): ?string
    {
        return $this->decimalSeparator;
    }

    protected function registerStatOverrideView(): void
    {
        $overridePath = __DIR__ . '/../resources/views/overrides';

        $finder = View::getFinder();

        $hints = $finder instanceof FileViewFinder
            ? $finder->getHints()['filament-widgets'] ?? []
            : [];

        if (in_array($overridePath, $hints, true)) {
            return;
        }

        View::prependNamespace('filament-widgets', $overridePath);
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
