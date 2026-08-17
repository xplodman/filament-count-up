<?php

use Xplodman\CountUp\CountUpPlugin;

it('exposes a stable plugin id', function () {
    expect(CountUpPlugin::make()->getId())->toBe('filament-count-up');
});
