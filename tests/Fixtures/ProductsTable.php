<?php

namespace Xplodman\CountUp\Tests\Fixtures;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;
use Xplodman\CountUp\Tables\Columns\CountUpColumn;
use Xplodman\CountUp\Tests\Models\Product;

class ProductsTable extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Product::query())
            ->columns([
                CountUpColumn::make('balance')
                    ->countUpDecimals(2)
                    ->countUpPrefix('EGP '),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
        <div>
            {{ $this->table }}
        </div>
        BLADE;
    }
}
