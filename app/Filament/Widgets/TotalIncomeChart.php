<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use App\Models\Transaction; // Import the Transaction model
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class TotalIncomeChart extends ChartWidget
{
    protected static ?string $heading = 'Sales Overview';
    protected static ?int $navigationSort = 2;


    protected function getData(): array
    {
        // Get the last 7 days
        $start = Carbon::now()->subDays(7);
        $end = Carbon::now();
    
        $data = Trend::model(Transaction::class)
            ->between(
                start: $start,
                end: $end,
            )
            ->perDay()
            ->count();
    
        if ($data->isEmpty()) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }
    
        return [
            'datasets' => [
                [
                    'label' => 'Transactions',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => Carbon::parse($value->date)->format('D')), // Display day of the week
        ];
    }
    

    protected function getType(): string
    {
        return 'line'; // Use a line chart to visualize the trend
    }
}
