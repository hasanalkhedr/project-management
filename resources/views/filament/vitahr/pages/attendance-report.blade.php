<x-filament-panels::page>

    <div class="space-y-6">

        {{-- Filters --}}

        <x-filament::section>

            <x-slot name="heading">
                {{ __('vitahr.report_filters') }}
            </x-slot>

            <form wire:submit.prevent="$refresh">
                {{ $this->form }}
            </form>

            <div class="mt-4 flex justify-end gap-2">

                <x-filament::button
                    wire:click="resetFilters"
                    color="gray"
                    icon="heroicon-o-arrow-path"
                >
                    {{ __('vitahr.reset_filters') }}
                </x-filament::button>

                <x-filament::button
                    wire:click="exportExcel"
                    color="success"
                    icon="heroicon-o-document-arrow-down"
                >
                    {{ __('vitahr.export_excel') }}
                </x-filament::button>

                <x-filament::button
                    wire:click="exportPdf"
                    color="danger"
                    icon="heroicon-o-document-text"
                >
                    {{ __('vitahr.export_pdf') }}
                </x-filament::button>

            </div>

        </x-filament::section>


        {{-- Statistics --}}

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">

            <x-filament::section>

                <div class="flex items-center justify-between">

                    <div>

                        <div class="text-sm text-gray-500">
                            {{ __('vitahr.employees') }}
                        </div>

                        <div class="mt-1 text-2xl font-bold">
                            {{ $this->getStats()['employees'] }}
                        </div>

                    </div>

                    <x-heroicon-o-users class="h-8 w-8 text-gray-400" />

                </div>

            </x-filament::section>


            <x-filament::section>

                <div class="flex items-center justify-between">

                    <div>

                        <div class="text-sm text-gray-500">
                            {{ __('vitahr.attendance_records_summary') }}
                        </div>

                        <div class="mt-1 text-2xl font-bold">
                            {{ $this->getStats()['records'] }}
                        </div>

                    </div>

                    <x-heroicon-o-calendar-days class="h-8 w-8 text-gray-400" />

                </div>

            </x-filament::section>


            <x-filament::section>

                <div class="flex items-center justify-between">

                    <div>

                        <div class="text-sm text-gray-500">
                            {{ __('vitahr.completed') }}
                        </div>

                        <div class="mt-1 text-2xl font-bold">
                            {{ $this->getStats()['completed'] }}
                        </div>

                    </div>

                    <x-heroicon-o-check-circle class="h-8 w-8 text-gray-400" />

                </div>

            </x-filament::section>


            <x-filament::section>

                <div class="flex items-center justify-between">

                    <div>

                        <div class="text-sm text-gray-500">
                            {{ __('vitahr.open') }}
                        </div>

                        <div class="mt-1 text-2xl font-bold">
                            {{ $this->getStats()['open'] }}
                        </div>

                    </div>

                    <x-heroicon-o-clock class="h-8 w-8 text-gray-400" />

                </div>

            </x-filament::section>

        </div>


        {{-- Attendance Table --}}

        <x-filament::section>

            {{ $this->table }}

        </x-filament::section>

    </div>

</x-filament-panels::page>
