@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 bg-[#f9fafb] focus:border-[#313e50] focus:ring-[#313e50] rounded-lg shadow-sm']) !!}>
