<?php

use Illuminate\Contracts\Support\Htmlable;
use Livewire\Livewire;
use Xplodman\CountUp\Tables\Columns\CountUpColumn;
use Xplodman\CountUp\Tests\Fixtures\ProductsTable;
use Xplodman\CountUp\Tests\Models\Product;

it('defaults to no explicit count up options', function () {
    $column = CountUpColumn::make('balance');

    expect($column->getCountUpDecimals())->toBeNull()
        ->and($column->getCountUpDuration())->toBeNull()
        ->and($column->getCountUpThousandsSeparator())->toBeNull()
        ->and($column->getCountUpDecimalSeparator())->toBeNull()
        ->and($column->getCountUpPrefix())->toBe('')
        ->and($column->getCountUpSuffix())->toBe('');
});

it('formats the state into a Htmlable so Filament never sanitizes away the alpine attributes', function () {
    $column = CountUpColumn::make('balance');

    $formatted = $column->formatState(1234.5);

    // Filament's TextColumn only skips `Str::sanitizeHtml()` (which strips
    // `x-data`/`x-text` as "unsafe" attributes) when the formatted state is
    // an instance of Htmlable — returning a plain string here, even with
    // ->html() set, would silently strip the alpine attributes and leave a
    // static, non-animated span.
    expect($formatted)->toBeInstanceOf(Htmlable::class);
});

it('accepts plain values for every count up option', function () {
    $column = CountUpColumn::make('balance')
        ->countUpDecimals(2)
        ->countUpDuration(500)
        ->countUpThousandsSeparator('.')
        ->countUpDecimalSeparator(',')
        ->countUpPrefix('EGP ')
        ->countUpSuffix(' only');

    expect($column->getCountUpDecimals())->toBe(2)
        ->and($column->getCountUpDuration())->toBe(500)
        ->and($column->getCountUpThousandsSeparator())->toBe('.')
        ->and($column->getCountUpDecimalSeparator())->toBe(',')
        ->and($column->getCountUpPrefix())->toBe('EGP ')
        ->and($column->getCountUpSuffix())->toBe(' only');
});

it('accepts closures for every count up option', function () {
    $column = CountUpColumn::make('balance')
        ->countUpDecimals(fn () => 3)
        ->countUpDuration(fn () => 1200)
        ->countUpPrefix(fn () => '$');

    expect($column->getCountUpDecimals())->toBe(3)
        ->and($column->getCountUpDuration())->toBe(1200)
        ->and($column->getCountUpPrefix())->toBe('$');
});

it('auto-replays by default (wire key defaults to true)', function () {
    $column = CountUpColumn::make('balance');

    expect($column->getCountUpWireKey())->toBeTrue();
});

it('accepts a plain wire key', function () {
    $column = CountUpColumn::make('balance')->countUpWireKey('balance-42');

    expect($column->getCountUpWireKey())->toBe('balance-42');
});

it('accepts opting out of auto-replay', function () {
    $column = CountUpColumn::make('balance')->countUpWireKey(false);

    expect($column->getCountUpWireKey())->toBeFalse();
});

it('accepts a closure wire key resolved against the current record', function () {
    $product = Product::create(['name' => 'Widget', 'balance' => 1234.5]);

    $column = CountUpColumn::make('balance')
        ->record($product)
        ->countUpWireKey(fn (Product $record) => "balance-{$record->id}-{$record->balance}");

    expect($column->getCountUpWireKey())->toBe("balance-{$product->id}-{$product->balance}");
});

it('renders the animated value inside a real filament table', function () {
    Product::create(['name' => 'Widget', 'balance' => 1234.5]);

    Livewire::test(ProductsTable::class)
        ->assertSee('EGP 1,234.50', escape: false)
        ->assertSeeHtml('<span')
        ->assertSeeHtml('x-data="countUp(');
});

it('renders a null balance as zero', function () {
    Product::create(['name' => 'Widget', 'balance' => null]);

    Livewire::test(ProductsTable::class)
        ->assertSee('EGP 0.00', escape: false);
});

it('renders a negative balance with a leading minus sign', function () {
    Product::create(['name' => 'Widget', 'balance' => -42.5]);

    Livewire::test(ProductsTable::class)
        ->assertSee('EGP -42.50', escape: false);
});
