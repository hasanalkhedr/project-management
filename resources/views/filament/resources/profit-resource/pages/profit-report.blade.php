<x-filament::page>
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
            <!-- Currency Summary (unchanged) -->
            <x-filament::card>
                <h3 class="text-lg font-semibold mb-4">{{ __('Currency Summary') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach ($this->getSummary()['by_currency'] as $currencyCode => $currencySummary)
                        <div>
                            <p class="text-gray-500">{{ $currencyCode }}</p>
                            <p class="text-2xl font-bold {{ $currencySummary >= 0 ? 'text-success-600' : 'text-danger-600' }}">
                                {{ number_format($currencySummary, 2) }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </x-filament::card>

            <!-- Updated Project Summary with Currency -->
            <x-filament::card>
                <h3 class="text-lg font-semibold mb-4">{{ __('Project Summary') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach ($this->getSummary()['by_project'] as $projectName => $projectData)
                        <div>
                            <p class="text-gray-500">{{ $projectName }}</p>
                            <p class="text-2xl font-bold {{ $projectData['total'] >= 0 ? 'text-success-600' : 'text-danger-600' }}">
                                {{ number_format($projectData['total'], 2) }} {{ $projectData['currency'] }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </x-filament::card>

            <!-- Transactions Table (unchanged) -->
            <x-filament::card>
                <h3 class="text-lg font-semibold mb-4">{{ __('Transaction Details') }}</h3>
                {{ $this->table }}
            </x-filament::card>
        </div>
    @endif
</x-filament::page>
