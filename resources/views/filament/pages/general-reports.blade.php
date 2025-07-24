<x-filament-panels::page>
    <form wire:submit.prevent="getReports">
        {{ $this->form }}

        <div class="flex gap-4 mt-4 justify-center">
            {{-- <x-filament::button type="submit" icon="heroicon-o-document-chart-bar">
                {{ __('general.generate_report') }}
            </x-filament::button> --}}

            @if ($this->data['start_date'] ?? false)
                <x-filament::button wire:click="exportToPdf" wire:loading.attr="disabled" icon="heroicon-o-arrow-down-tray"
                    color="success">
                    {{ __('general.export_pdf') }}
                </x-filament::button>
            @endif
        </div>
    </form>

    {{-- @if ($this->data['start_date'] ?? false)
        @php
            $reports = $this->getReports();
            $hasData = count($reports['currencySummaries']) > 0;

            // Combine and sort transactions by date
            $transactions = collect();
            foreach ($reports['projectSummaries'] as $project) {
                foreach ($project['expenses_details'] ?? [] as $expense) {
                    $transactions->push([
                        'type' => 'expense',
                        'date' => $expense['date'],
                        'project' => $project['name'],
                        'description' => $expense['description'],
                        'amount' => $expense['amount'],
                        'currency' => $expense['currency_code'],
                        'timestamp' => strtotime($expense['date']),
                    ]);
                }
                foreach ($project['payments_details'] ?? [] as $payment) {
                    $transactions->push([
                        'type' => 'payment',
                        'date' => $payment['date'],
                        'project' => $project['name'],
                        'description' => $payment['description'],
                        'amount' => $payment['amount'],
                        'currency' => $payment['currency_code'],
                        'timestamp' => strtotime($payment['date']),
                    ]);
                }
            }

            // Sort by date (newest first)
            $sortedTransactions = $transactions->sortBy('timestamp');
        @endphp

        <div class="mt-8 space-y-6">
            @if ($hasData)
                <x-filament::card>
                    <h2 class="text-xl font-bold mb-4">{{ __('general.reports') }}</h2>
                    <p class="text-sm text-gray-500 mb-4">
                        {{ __('general.report_period') }}:
                        {{ \Carbon\Carbon::parse($this->data['start_date'])->translatedFormat('j F Y') }} -
                        {{ \Carbon\Carbon::parse($this->data['end_date'])->translatedFormat('j F Y') }}
                        @if ($this->data['currency_id'] !== 'all')
                            | {{ __('general.currency') }}:
                            {{ \App\Models\Currency::find($this->data['currency_id'])->name }}
                        @endif
                        | {{ __('general.report_type') }}:
                        {{ match ($this->data['report_type']) {
                            'both' => __('general.both_payments_and_expenses'),
                            'payments' => __('general.payments_only'),
                            'expenses' => __('general.expenses_only'),
                            default => $this->data['report_type'],
                        } }}
                    </p>

                    <div class="grid grid-cols-1 gap-6">
                        @foreach ($reports['currencySummaries'] as $currency => $summary)
                            <x-filament::card>
                                <h3 class="text-lg font-semibold mb-4">{{ $currency }} {{ __('general.summary') }}
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    @if ($this->data['report_type'] !== 'payments')
                                        <div>
                                            <p class="text-gray-500">{{ __('general.total_expenses') }}</p>
                                            <p class="text-2xl font-bold text-danger-600">
                                                {{ number_format($summary['expenses'], 2) }} {{ $currency }}
                                            </p>
                                        </div>
                                    @endif
                                    @if ($this->data['report_type'] !== 'expenses')
                                        <div>
                                            <p class="text-gray-500">{{ __('general.total_payments') }}</p>
                                            <p class="text-2xl font-bold text-success-600">
                                                {{ number_format($summary['payments'], 2) }} {{ $currency }}
                                            </p>
                                        </div>
                                    @endif
                                    @if ($this->data['report_type'] === 'both')
                                        <div>
                                            <p class="text-gray-500">{{ __('general.net_profit') }}</p>
                                            <p
                                                class="text-2xl font-bold {{ $summary['profit'] >= 0 ? 'text-success-600' : 'text-danger-600' }}">
                                                {{ number_format($summary['profit'], 2) }} {{ $currency }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </x-filament::card>
                        @endforeach
                    </div>
                </x-filament::card>

                <x-filament::card>
                    <h3 class="text-lg font-semibold mb-4">{{ __('general.project_performance') }}</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('general.project') }}
                                    </th>
                                    @foreach (array_keys($reports['currencySummaries']) as $currency)
                                        <th colspan="{{ $this->data['report_type'] === 'both' ? 3 : 1 }}"
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $currency }}
                                        </th>
                                    @endforeach
                                </tr>
                                <tr>
                                    <th></th>
                                    @foreach (array_keys($reports['currencySummaries']) as $currency)
                                        @if ($this->data['report_type'] !== 'payments')
                                            <th
                                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ __('general.expenses') }}
                                            </th>
                                        @endif
                                        @if ($this->data['report_type'] !== 'expenses')
                                            <th
                                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ __('general.payments') }}
                                            </th>
                                        @endif
                                        @if ($this->data['report_type'] === 'both')
                                            <th
                                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ __('general.profit') }}
                                            </th>
                                        @endif
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($reports['projectSummaries'] as $project)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $project['name'] }}</td>
                                        @foreach (array_keys($reports['currencySummaries']) as $currency)
                                            @php
                                                $currencyData = $project['currencies'][$currency] ?? [
                                                    'expenses' => 0,
                                                    'payments' => 0,
                                                    'profit' => 0,
                                                ];
                                            @endphp
                                            @if ($this->data['report_type'] !== 'payments')
                                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                                    {{ number_format($currencyData['expenses'], 2) }}
                                                </td>
                                            @endif
                                            @if ($this->data['report_type'] !== 'expenses')
                                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                                    {{ number_format($currencyData['payments'], 2) }}
                                                </td>
                                            @endif
                                            @if ($this->data['report_type'] === 'both')
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-right font-semibold {{ $currencyData['profit'] >= 0 ? 'text-success-600' : 'text-danger-600' }}">
                                                    {{ number_format($currencyData['profit'], 2) }}
                                                </td>
                                            @endif
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-filament::card>
                <!-- Combined Transactions Table -->
                @if ($sortedTransactions->isNotEmpty())
                    <x-filament::card>
                        <h3 class="text-lg font-semibold mb-4">{{ __('general.transactions_history') }}</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('general.date') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('general.type') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('general.project') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('general.description') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('general.amount') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('general.currency') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($sortedTransactions as $transaction)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ \Carbon\Carbon::parse($transaction['date'])->format('Y-m-d') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="px-2 py-1 text-xs rounded-full
                                                {{ $transaction['type'] === 'payment' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $transaction['type'] === 'payment' ? __('general.payment') : __('general.expense') }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $transaction['project'] }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $transaction['description'] }}
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-right
                                            {{ $transaction['type'] === 'payment' ? 'text-success-600' : 'text-danger-600' }}">
                                                {{ number_format($transaction['amount'], 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $transaction['currency'] }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </x-filament::card>
                @else
                    <x-filament::card>
                        <div class="p-6 text-center">
                            <x-heroicon-o-information-circle class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900">
                                {{ __('general.no_transactions_found') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ __('general.no_transactions_matching_filters') }}
                            </p>
                        </div>
                    </x-filament::card>
                @endif
            @else
                <x-filament::card>
                    <div class="p-6 text-center">
                        <x-heroicon-o-information-circle class="mx-auto h-12 w-12 text-gray-400" />
                        <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('general.no_data_found') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ __('general.no_data_matching_filters') }}
                        </p>
                    </div>
                </x-filament::card>
            @endif
        </div>
    @endif --}}
</x-filament-panels::page>
