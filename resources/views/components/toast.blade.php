@props([
    'type' => 'message', // message | error
    'message' => '',
])

@php
    $isError = $type === 'error';
    $baseClasses = 'pointer-events-auto w-full max-w-sm rounded-xl shadow-lg border px-4 py-3 flex items-start gap-3 text-sm';
    $colorClasses = $isError
        ? 'bg-red-50 border-red-200 text-red-800'
        : 'bg-emerald-50 border-emerald-200 text-emerald-800';
@endphp

<div
    x-data="{ open: true }"
    x-show="open"
    x-transition.opacity.duration.150ms
    x-transition.scale.duration.150ms
    x-init="setTimeout(() => open = false, 4000)"
    class="{{ $baseClasses }} {{ $colorClasses }}"
>
    <div class="mt-0.5">
        @if($isError)
            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-red-100 text-red-700">
                !
            </span>
        @else
            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                ✓
            </span>
        @endif
    </div>
    <div class="flex-1">
        <p class="font-medium">
            {{ $slot->isNotEmpty() ? $slot : $message }}
        </p>
    </div>
    <button
        type="button"
        class="ml-2 text-xs text-gray-400 hover:text-gray-600"
        @click="open = false"
    >
        fechar
    </button>
</div>

