<x-filament::page>
    <h2>{{$this->record->name}}</h2>
    <x-filament-panels::form wire:submit>
        {{ $this->form }}
        <div class="flex gap-4 mt-4 justify-center">
            <x-filament::button type="submit">
                Generate Report
            </x-filament::button>
            <x-filament::button
                wire:click="exportToPdf"
                icon="heroicon-o-arrow-down-tray"
                color="success">
                Export to PDF
            </x-filament::button>
        </div>
    </x-filament-panels::form>

    @if($this->data['start_date'] ?? false)
        <div class="space-y-6 mt-6">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-filament::card>
                    <p class="text-gray-500">Total Expenses</p>
                    <p class="text-2xl font-bold">
                        {{ number_format($this->getSummary()['expenses'], 2) }}
                    </p>
                </x-filament::card>

                <x-filament::card>
                    <p class="text-gray-500">Total Payments</p>
                    <p class="text-2xl font-bold">
                        {{ number_format($this->getSummary()['payments'], 2) }}
                    </p>
                </x-filament::card>

                <x-filament::card>
                    <p class="text-gray-500">Net Profit</p>
                    <p class="text-2xl font-bold {{ $this->getSummary()['profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ number_format($this->getSummary()['profit'], 2) }}
                    </p>
                </x-filament::card>
            </div>

            <!-- Expenses Table -->
            <x-filament::card>
                <h3 class="text-lg font-semibold mb-4">Expenses Details</h3>
                {{ $this->table }}
            </x-filament::card>

            {{-- <!-- Payments Table -->
            <x-filament::card>
                <h3 class="text-lg font-semibold mb-4">Payments Received</h3>
                {{ $this->paymentsTable }}
            </x-filament::card> --}}
        </div>
    @endif
</x-filament::page>
