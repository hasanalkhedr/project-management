@php
    // Ensure we have a record and it has the relationships loaded
    $record = $this->record;

    if (!$record->relationLoaded('expenses')) {
        $record->load('expenses.currency');
    }

    if (!$record->relationLoaded('payments')) {
        $record->load('payments.currency');
    }

    $expensesByCurrency = $record->expenses->groupBy('currency.code');
    $paymentsByCurrency = $record->payments->groupBy('currency.code');
    $allCurrencies = $expensesByCurrency->keys()->merge($paymentsByCurrency->keys())->unique()->sort();
@endphp

@if ($allCurrencies->isNotEmpty())
    <div class="space-y-6 mt-6">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 gap-6">
            <!-- Currency-Specific Summaries -->
            @foreach ($allCurrencies as $currencyCode)
                @php
                    $expenses = $expensesByCurrency->get($currencyCode, collect())->sum('amount');
                    $payments = $paymentsByCurrency->get($currencyCode, collect())->sum('amount');
                    $profit = $payments - $expenses;
                @endphp

                <x-filament::card>
                    <h3 class="text-lg font-semibold mb-4">{{ $currencyCode }} {{ __('Summary') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-gray-500">{{ __('Expenses') }}</p>
                            <p class="text-2xl font-bold text-danger-600">
                                {{ number_format($expenses, 2) }} {{ $currencyCode }}
                            </p>
                        </div>

                        <div>
                            <p class="text-gray-500">{{ __('Payments') }}</p>
                            <p class="text-2xl font-bold text-success-600">
                                {{ number_format($payments, 2) }} {{ $currencyCode }}
                            </p>
                        </div>

                        <div>
                            <p class="text-gray-500">{{ __('Profit') }}</p>
                            <p
                                class="text-2xl font-bold {{ $profit >= 0 ? 'text-success-600' : 'text-danger-600' }}">
                                {{ number_format($profit, 2) }} {{ $currencyCode }}
                            </p>
                        </div>
                    </div>
                </x-filament::card>
            @endforeach
        </div>
    @else
        <div class="text-gray-500">No currency data available</div>
@endif
