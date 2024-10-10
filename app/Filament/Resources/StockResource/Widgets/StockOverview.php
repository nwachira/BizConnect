<?php

namespace App\Filament\Resources\StockResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Stock;

class StockOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalStock = Stock::sum('quantity');

        return [
            Stat::make('Total Stock Remaining', $totalStock)
                ->description('Total quantity of all stock items')
                ->color('success'),
            // Add more stats as needed
        ];
    }
}
