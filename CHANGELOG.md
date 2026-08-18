# Changelog

All notable changes to `filament-count-up` will be documented in this file.

## 1.0.3 - 2026-08-18

- Added `CountUpPlugin` support for auto-animating every `Stat::make()` value on a panel with zero per-widget changes: registering the plugin overrides `filament-widgets::stats-overview-widget.stat` to route each stat's value through `CountUpStat::animate()`, which detects and animates numeric tokens (plain numbers, `number_format()`-built strings, ratios, suffixed values like `"180ms"`) while leaving the rest of the string as static text.
- Added fluent plugin configuration: `autoAnimateStats()`, `decimals()`, `duration()`, `thousandsSeparator()`, `decimalSeparator()`.

## 1.0.2 - 2026-08-18

- `<x-count-up>` and `CountUpColumn` now default to generating a fresh `wire:key` on every render, so the count-up animation replays after a Livewire refresh/poll instead of getting stuck at its old value (Alpine keeps already-initialized components across a morph unless the key changes). Pass a string for a stable custom key, or `false` to opt back into the default morph-preserve behaviour.

## 1.0.0 - 202X-XX-XX

- initial release
