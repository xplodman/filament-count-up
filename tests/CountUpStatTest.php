<?php

use Illuminate\Support\Facades\Blade;
use Xplodman\CountUp\Facades\CountUpStat;

it('renders the default number of decimals from config', function () {
    $html = (string) CountUpStat::make(1234);

    expect($html)->toContain('1,234');
});

it('renders a custom number of decimals', function () {
    $html = (string) CountUpStat::make(1234.567, decimals: 2);

    expect($html)->toContain('1,234.57');
});

it('formats negative numbers with a leading minus sign', function () {
    $html = (string) CountUpStat::make(-1234, decimals: 0);

    expect($html)->toContain('-1,234');
});

it('treats a null value as zero', function () {
    $html = (string) CountUpStat::make(null, decimals: 2);

    expect($html)->toContain('0.00');
});

it('treats a non numeric value as zero instead of erroring', function () {
    $html = (string) CountUpStat::make('not-a-number', decimals: 0);

    expect($html)->toContain('>0<');
});

it('applies a prefix and suffix around the formatted number', function () {
    $html = (string) CountUpStat::make(50, decimals: 0, prefix: 'EGP ', suffix: '%');

    expect($html)->toContain('EGP 50%');
});

it('supports custom thousands and decimal separators', function () {
    $html = (string) CountUpStat::make(1234.5, decimals: 1, thousandsSeparator: '.', decimalSeparator: ',');

    expect($html)->toContain('1.234,5');
});

it('falls back to the config defaults when no options are given', function () {
    config()->set('count-up.decimals', 3);
    config()->set('count-up.duration', 2500);

    $html = (string) CountUpStat::make(1);

    expect($html)
        ->toContain('1.000')
        ->and($html)->toContain('2500');
});

it('encodes the alpine config used to drive the animation', function () {
    $html = (string) CountUpStat::make(1234.5, decimals: 2, duration: 750, prefix: '$');

    expect($html)
        ->toContain('x-data="countUp(')
        ->and($html)->toContain('1234.5')
        ->and($html)->toContain('750');
});

it('is renderable through the short blade component alias', function () {
    $html = Blade::render('<x-count-up :value="1234" class="text-3xl" />');

    expect($html)
        ->toContain('1,234')
        ->and($html)->toContain('text-3xl')
        ->and($html)->toContain('fi-count-up');
});

it('is renderable through the namespaced component tag', function () {
    $html = Blade::render('<x-filament-count-up::count-up :value="99" decimals="0" />');

    expect($html)->toContain('99');
});

it('escapes html special characters in the prefix and suffix for the static fallback text', function () {
    $html = (string) CountUpStat::make(1, decimals: 0, suffix: '<script>');

    expect($html)
        ->not->toContain('<script>')
        ->and($html)->toContain('&lt;script&gt;');
});

it('uses a given wire:key, so livewire replays the animation on refresh', function () {
    $html = (string) CountUpStat::make(1234, wireKey: 'treasury-balance-1234');

    expect($html)->toContain('wire:key="treasury-balance-1234"');
});

it('auto-generates a fresh wire:key by default, so it always replays on a livewire refresh', function () {
    $html1 = (string) CountUpStat::make(1234);
    $html2 = (string) CountUpStat::make(1234);

    expect($html1)->toContain('wire:key="count-up-');

    preg_match('/wire:key="([^"]+)"/', $html1, $match1);
    preg_match('/wire:key="([^"]+)"/', $html2, $match2);

    expect($match1[1])->not->toBe($match2[1]);
});

it('omits the wire:key attribute entirely when opted out', function () {
    $html = (string) CountUpStat::make(1234, wireKey: false);

    expect($html)->not->toContain('wire:key');
});
