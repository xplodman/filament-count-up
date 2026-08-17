@props([
    'value' => 0,
    'decimals' => null,
    'duration' => null,
    'thousandsSeparator' => null,
    'decimalSeparator' => null,
    'prefix' => '',
    'suffix' => '',
    // true (default): auto-generate a fresh wire:key on every render, so a
    // Livewire refresh/poll always replays the animation instead of Alpine's
    // morph preserving the old, already-settled value. Pass a string for a
    // stable custom key, or false to opt out of forced re-animation.
    'wireKey' => true,
])

@php
    $decimals ??= config('count-up.decimals', 0);
    $duration ??= config('count-up.duration', 1000);
    $thousandsSeparator ??= config('count-up.thousands_separator', ',');
    $decimalSeparator ??= config('count-up.decimal_separator', '.');

    $resolvedWireKey = match (true) {
        $wireKey === true => 'count-up-' . \Illuminate\Support\Str::random(8),
        is_string($wireKey) && $wireKey !== '' => $wireKey,
        default => null,
    };

    $numericValue = is_numeric($value) ? (float) $value : 0.0;

    $jsConfig = [
        'value' => $numericValue,
        'decimals' => (int) $decimals,
        'duration' => (int) $duration,
        'thousandsSeparator' => $thousandsSeparator,
        'decimalSeparator' => $decimalSeparator,
        'prefix' => $prefix,
        'suffix' => $suffix,
    ];

    $initialText = $prefix . number_format($numericValue, (int) $decimals, $decimalSeparator, $thousandsSeparator) . $suffix;
@endphp
<span
    {{ $attributes->class(['fi-count-up']) }}
    @if ($resolvedWireKey)
        wire:key="{{ $resolvedWireKey }}"
    @endif
    x-data="countUp(@js($jsConfig))"
    x-text="displayValue"
>{{ $initialText }}</span>
