<x-filament-panels::page>

    <x-filament::page>
        <form wire:submit.prevent="getReports">
            {{ $this->form }}

            <x-filament::button type="submit" class="mt-4">
                عرض التقرير
            </x-filament::button>
        </form>

        <div class="mt-8 space-y-6">
            <x-filament::card>
                <h2 class="text-xl font-bold mb-4">التقارير العامة لجميع المشاريع</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold mb-2">إجمالي النفقات حسب العملة</h3>
                        <ul class="space-y-2">
                            @foreach ($this->getReports()['expensesByCurrency'] as $currency => $amount)
                                <li class="flex justify-between">
                                    <span>{{ $currency }}:</span>
                                    <span>{{ number_format($amount, 2) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold mb-2">إجمالي الدفعات حسب العملة</h3>
                        <ul class="space-y-2">
                            @foreach ($this->getReports()['paymentsByCurrency'] as $currency => $amount)
                                <li class="flex justify-between">
                                    <span>{{ $currency }}:</span>
                                    <span>{{ number_format($amount, 2) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t">
                    <h3 class="text-lg font-semibold mb-2">الملخص العام</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <x-filament::card>
                            <p class="text-gray-500">إجمالي النفقات</p>
                            <p class="text-2xl font-bold">
                                {{ array_sum($this->getReports()['expensesByCurrency']->toArray()) }}</p>
                        </x-filament::card>

                        <x-filament::card>
                            <p class="text-gray-500">إجمالي الدفعات</p>
                            <p class="text-2xl font-bold">
                                {{ array_sum($this->getReports()['paymentsByCurrency']->toArray()) }}</p>
                        </x-filament::card>

                        <x-filament::card>
                            <p class="text-gray-500">صافي الربح</p>
                            <p class="text-2xl font-bold">
                                {{ array_sum($this->getReports()['paymentsByCurrency']->toArray()) - array_sum($this->getReports()['expensesByCurrency']->toArray()) }}
                            </p>
                        </x-filament::card>
                    </div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <h3 class="text-lg font-semibold mb-4">أداء المشاريع</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    المشروع</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    النفقات</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    الدفعات</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    الربح</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($this->getReports()['projects'] as $project)
                                @php
                                    $projectExpenses = $project->expenses
                                        ->when(
                                            $this->data['start_date'],
                                            fn($query, $date) => $query->where('date', '>=', $date),
                                        )
                                        ->when(
                                            $this->data['end_date'],
                                            fn($query, $date) => $query->where('date', '<=', $date),
                                        )
                                        ->sum('amount');

                                    $projectPayments = $project->payments
                                        ->when(
                                            $this->data['start_date'],
                                            fn($query, $date) => $query->where('date', '>=', $date),
                                        )
                                        ->when(
                                            $this->data['end_date'],
                                            fn($query, $date) => $query->where('date', '<=', $date),
                                        )
                                        ->sum('amount');

                                    $profit = $projectPayments - $projectExpenses;
                                @endphp
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $project->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ number_format($projectExpenses, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ number_format($projectPayments, 2) }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap font-semibold {{ $profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ number_format($profit, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-filament::card>
        </div>
    </x-filament::page>

</x-filament-panels::page>
