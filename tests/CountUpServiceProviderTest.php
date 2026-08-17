<?php

use Filament\Support\Facades\FilamentAsset;

it('registers the compiled script as a filament asset', function () {
    $registered = collect(FilamentAsset::getScripts())
        ->contains(fn ($asset) => $asset->getId() === 'filament-count-up-scripts');

    expect($registered)->toBeTrue();
});

it('ships a built, non empty javascript asset', function () {
    $path = __DIR__ . '/../resources/dist/filament-count-up.js';

    expect(file_exists($path))->toBeTrue()
        ->and(filesize($path))->toBeGreaterThan(0);
});
