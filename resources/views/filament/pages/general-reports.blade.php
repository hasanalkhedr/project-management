<x-filament-panels::page>
        <form wire:submit.prevent="getReports">
            {{ $this->form }}

            <div class="flex gap-4 mt-4 justify-center">
                <x-filament::button type="submit" icon="heroicon-o-document-chart-bar">
                    {{ __('general.generate_report') }}
                </x-filament::button>

                @if($this->data['start_date'] ?? false)
                    <x-filament::button
                        wire:click="exportToPdf"
                        wire:loading.attr="disabled"
                        icon="heroicon-o-arrow-down-tray"
                        color="success">
                        {{ __('general.export_pdf') }}
                    </x-filament::button>
                @endif
            </div>
        </form>

        @if($this->data['start_date'] ?? false)
            <div class="mt-8 space-y-6">
                <x-filament::card>
                    <h2 class="text-xl font-bold mb-4">{{ __('general.reports') }}</h2>

                    <div class="grid grid-cols-1 gap-6">
                        @foreach($this->getReports()['currencySummaries'] as $currency => $summary)
                            <x-filament::card>
                                <h3 class="text-lg font-semibold mb-4">{{ $currency }} {{ __('general.summary') }}</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <p class="text-gray-500">{{ __('general.total_expenses') }}</p>
                                        <p class="text-2xl font-bold text-danger-600">
                                            {{ number_format($summary['expenses'], 2) }} {{ $currency }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">{{ __('general.total_payments') }}</p>
                                        <p class="text-2xl font-bold text-success-600">
                                            {{ number_format($summary['payments'], 2) }} {{ $currency }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">{{ __('general.net_profit') }}</p>
                                        <p class="text-2xl font-bold {{ $summary['profit'] >= 0 ? 'text-success-600' : 'text-danger-600' }}">
                                            {{ number_format($summary['profit'], 2) }} {{ $currency }}
                                        </p>
                                    </div>
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
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('general.project') }}
                                    </th>
                                    @foreach(array_keys($this->getReports()['currencySummaries']->toArray()) as $currency)
                                        <th colspan="3" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $currency }}
                                        </th>
                                    @endforeach
                                </tr>
                                <tr>
                                    <th></th>
                                    @foreach(array_keys($this->getReports()['currencySummaries']->toArray()) as $currency)
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('general.expenses') }}
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('general.payments') }}
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('general.profit') }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($this->getReports()['projectSummaries'] as $project)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $project['name'] }}</td>
                                        @foreach(array_keys($this->getReports()['currencySummaries']->toArray()) as $currency)
                                            @php
                                                $currencyData = $project['currencies'][$currency] ?? [
                                                    'expenses' => 0,
                                                    'payments' => 0,
                                                    'profit' => 0
                                                ];
                                            @endphp
                                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                                {{ number_format($currencyData['expenses'], 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                                {{ number_format($currencyData['payments'], 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right font-semibold {{ $currencyData['profit'] >= 0 ? 'text-success-600' : 'text-danger-600' }}">
                                                {{ number_format($currencyData['profit'], 2) }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-filament::card>
            </div>
        @endif
</x-filament-panels::page>
