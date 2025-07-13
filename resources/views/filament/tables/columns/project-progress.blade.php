{{-- resources/views/filament/tables/columns/project-progress.blade.php --}}
@props([
    'state' => 0, // Default to 0 if not provided
])

@php
    // Ensure $state is always a number
    $percentage = min((float)$state, 100);
    $percentage = max($percentage, 0);

    // Determine color class
    $colorClass = match (true) {
        $percentage >= 100 => 'bg-success-500',
        $percentage >= 70 => 'bg-primary-500',
        $percentage >= 40 => 'bg-warning-500',
        default => 'bg-danger-500',
    };
@endphp

<div class="flex items-center gap-2">
    <div class="w-full bg-gray-200 rounded-full h-2.5">
        <div
            class="h-2.5 rounded-full {{ $colorClass }}"
            style="width: {{ $percentage }}%"
        ></div>
    </div>
    <span class="text-sm text-gray-600">{{ round($percentage) }}%</span>
</div>
