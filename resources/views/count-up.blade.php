@props([
    'value' => 0,
    'decimals' => null,
    'duration' => null,
    'thousandsSeparator' => null,
    'decimalSeparator' => null,
    'prefix' => '',
    'suffix' => '',
])

@php
    $decimals ??= config('count-up.decimals', 0);
    $duration ??= config('count-up.duration', 1000);
    $thousandsSeparator ??= config('count-up.thousands_separator', ',');
    $decimalSeparator ??= config('count-up.decimal_separator', '.');

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
    x-data="countUp(@js($jsConfig))"
    x-text="displayValue"
>{{ $initialText }}</span>
