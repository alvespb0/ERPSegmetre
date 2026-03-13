<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2 bg-[#313e50] border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#3a435e] focus:bg-[#3a435e] active:bg-[#1f2733] focus:outline-none focus:ring-2 focus:ring-[#6c6f7f] focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
