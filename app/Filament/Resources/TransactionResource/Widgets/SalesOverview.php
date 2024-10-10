<?php

namespace App\Filament\Resources\TransactionResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Transaction;

class SalesOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalIncome = Transaction::sum('amount_earned');

        // Format the total income as Kenyan Shillings
        $formattedIncome = 'KSh ' . number_format($totalIncome, 2);

        return [
            Stat::make('Total Income', $formattedIncome)
                ->description('Total amount earned from all transactions')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success')
        ];
    }
}
