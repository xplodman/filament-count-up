<?php

namespace Xplodman\CountUp\Tables\Columns;

use Closure;
use Filament\Tables\Columns\TextColumn;
use Xplodman\CountUp\Facades\CountUpStat;

class CountUpColumn extends TextColumn
{
    protected int | Closure | null $countUpDecimals = null;

    protected int | Closure | null $countUpDuration = null;

    protected string | Closure | null $countUpThousandsSeparator = null;

    protected string | Closure | null $countUpDecimalSeparator = null;

    protected string | Closure $countUpPrefix = '';

    protected string | Closure $countUpSuffix = '';

    protected bool | string | Closure $countUpWireKey = true;

    public function getState(): mixed
    {
        $state = parent::getState();

        // A null/blank state would otherwise make Filament render the
        // column's placeholder instead of calling `formatStateUsing()`,
        // skipping the animation entirely. Treat it as zero instead.
        return is_numeric($state) ? $state : 0;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->formatStateUsing(fn ($state) => CountUpStat::make(
            value: $state,
            decimals: $this->getCountUpDecimals(),
            duration: $this->getCountUpDuration(),
            thousandsSeparator: $this->getCountUpThousandsSeparator(),
            decimalSeparator: $this->getCountUpDecimalSeparator(),
            prefix: $this->getCountUpPrefix(),
            suffix: $this->getCountUpSuffix(),
            wireKey: $this->getCountUpWireKey(),
        ));
    }

    public function countUpDecimals(int | Closure | null $decimals): static
    {
        $this->countUpDecimals = $decimals;

        return $this;
    }

    public function countUpDuration(int | Closure | null $duration): static
    {
        $this->countUpDuration = $duration;

        return $this;
    }

    public function countUpThousandsSeparator(string | Closure | null $separator): static
    {
        $this->countUpThousandsSeparator = $separator;

        return $this;
    }

    public function countUpDecimalSeparator(string | Closure | null $separator): static
    {
        $this->countUpDecimalSeparator = $separator;

        return $this;
    }

    public function countUpPrefix(string | Closure $prefix): static
    {
        $this->countUpPrefix = $prefix;

        return $this;
    }

    public function countUpSuffix(string | Closure $suffix): static
    {
        $this->countUpSuffix = $suffix;

        return $this;
    }

    /**
     * By default (`true`), a fresh key is generated on every render so the
     * animation always replays on a Livewire refresh/poll instead of
     * Alpine's morph preserving the old, already-settled value. Pass a
     * string for a stable custom key, or `false` to opt out.
     */
    public function countUpWireKey(bool | string | Closure $wireKey): static
    {
        $this->countUpWireKey = $wireKey;

        return $this;
    }

    public function getCountUpDecimals(): ?int
    {
        return $this->evaluate($this->countUpDecimals);
    }

    public function getCountUpDuration(): ?int
    {
        return $this->evaluate($this->countUpDuration);
    }

    public function getCountUpThousandsSeparator(): ?string
    {
        return $this->evaluate($this->countUpThousandsSeparator);
    }

    public function getCountUpDecimalSeparator(): ?string
    {
        return $this->evaluate($this->countUpDecimalSeparator);
    }

    public function getCountUpPrefix(): string
    {
        return $this->evaluate($this->countUpPrefix) ?? '';
    }

    public function getCountUpSuffix(): string
    {
        return $this->evaluate($this->countUpSuffix) ?? '';
    }

    public function getCountUpWireKey(): bool | string
    {
        return $this->evaluate($this->countUpWireKey);
    }
}
