<x-filament::page>
    <h2 class="text-2xl font-bold">{{ $this->record->name }}</h2>
    <x-filament-panels::form wire:submit.prevent="generateReport">
        {{ $this->form }}
        <div class="flex gap-4 mt-4 justify-center">
            <x-filament::button type="submit" icon="heroicon-o-document-chart-bar">
                {{ __('Generate Report') }}
            </x-filament::button>
            <x-filament::button wire:click.prevent="exportToPdf" wire:loading.attr="disabled"
                icon="heroicon-o-arrow-down-tray" color="success">
                {{ __('Export to PDF') }}
            </x-filament::button>
        </div>
    </x-filament-panels::form>

    @if ($this->data['start_date'] ?? false)
        <div class="space-y-6 mt-6">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 gap-6">
                <!-- Currency-Specific Summaries -->
                @foreach ($this->getSummary()['by_currency'] as $currencyCode => $currencySummary)
                    <x-filament::card>
                        <h3 class="text-lg font-semibold mb-4">{{ $currencyCode }} {{ __('Summary') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <p class="text-gray-500">{{ __('Expenses') }}</p>
                                <p class="text-2xl font-bold text-danger-600">
                                    {{ number_format($currencySummary['expenses'], 2) }} {{ $currencyCode }}
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-500">{{ __('Payments') }}</p>
                                <p class="text-2xl font-bold text-success-600">
                                    {{ number_format($currencySummary['payments'], 2) }} {{ $currencyCode }}
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-500">{{ __('Profit') }}</p>
                                <p
                                    class="text-2xl font-bold {{ $currencySummary['profit'] >= 0 ? 'text-success-600' : 'text-danger-600' }}">
                                    {{ number_format($currencySummary['profit'], 2) }} {{ $currencyCode }}
                                </p>
                            </div>
                        </div>
                    </x-filament::card>
                @endforeach
            </div>

            <!-- Expenses Table -->
            <x-filament::card>
                <h3 class="text-lg font-semibold mb-4">{{ __('Transaction Details') }}</h3>
                {{ $this->table }}
            </x-filament::card>
        </div>
    @endif
</x-filament::page>
