<?php

namespace App\Filament\Resources\TransactionResource\Widgets;

use App\Models\Transaction;
use App\Models\Currency;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class TransStats extends BaseWidget
{
    protected function getHeader(): ?string
    {
        return __('Special Payments/Expenses Summary'); // Your header title
    }

    // protected function getHeaderIcon(): ?string
    // {
    //     return 'heroicon-o-user-group'; // Optional icon
    // }
    protected function getStats(): array
    {
        $stats = [];
        $currencies = Currency::whereHas('transactions')->get();

        foreach ($currencies as $currency) {
            $expenses = Transaction::ofType('expense')
                ->where('currency_id', $currency->id)
                ->sum('amount');

            $payments = Transaction::ofType('payment')
                ->where('currency_id', $currency->id)
                ->sum('amount');

            $net = $payments - $expenses;

            // Add currency-specific stats
            $stats[] = Stat::make(__("Total Special Payments") . " ({$currency->code})", number_format($payments, 2))
                ->description(__("Incoming payments in") . " {$currency->name}")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success');

            $stats[] = Stat::make(
                __("Total Special Expenses") . " ({$currency->code})",
                number_format($expenses, 2)
            )
                ->description(__("Outgoing expenses in") . " {$currency->name}")
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger');

            $stats[] = Stat::make(
                __("Net Balance") . " ({$currency->code})",
                number_format($net, 2)
            )
                ->description($net >= 0 ? __('Positive balance') : __('Negative balance'))
                ->descriptionIcon($net >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($net >= 0 ? 'success' : 'danger');
        }
        return $stats;
    }
}
