<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ERP Clinic') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-background text-foreground">
        <div class="min-h-screen bg-[var(--erp-background)]">
            <div class="fixed left-0 top-0 h-screen w-64 bg-[#313e50] text-white flex flex-col shadow-lg z-20">
                <div class="p-6 border-b border-white/10">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-[#6c6f7f] rounded-lg flex items-center justify-center">
                            <x-application-logo class="w-7 h-7 text-white" />
                        </div>
                        <div>
                            <h1 class="text-lg font-semibold">{{ config('app.name', 'OccHealth') }}</h1>
                            <p class="text-xs text-white/70">ERP System</p>
                        </div>
                    </div>
                </div>

                <nav class="flex-1 p-4 overflow-y-auto">
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('dashboard') }}"
                               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('dashboard') ? 'bg-[#6c6f7f] text-white shadow-md' : 'text-white/80 hover:bg-[#455561] hover:text-white' }}">
                                <span class="w-5 h-5 rounded-md bg-white/10"></span>
                                <span class="text-sm">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('erp.accounts-receivable') }}"
                               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('erp.accounts-receivable') ? 'bg-[#6c6f7f] text-white shadow-md' : 'text-white/80 hover:bg-[#455561] hover:text-white' }}">
                                <span class="w-5 h-5 rounded-md bg-white/10"></span>
                                <span class="text-sm">Accounts Receivable</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('erp.accounts-payable') }}"
                               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('erp.accounts-payable') ? 'bg-[#6c6f7f] text-white shadow-md' : 'text-white/80 hover:bg-[#455561] hover:text-white' }}">
                                <span class="w-5 h-5 rounded-md bg-white/10"></span>
                                <span class="text-sm">Accounts Payable</span>
                            </a>
                        </li>
                    </ul>
                </nav>

                <div class="p-4 border-t border-white/10 text-xs text-white/60 text-center">
                    <p>Occupational Health Clinic</p>
                    <p class="mt-1">© {{ date('Y') }} All rights reserved</p>
                </div>
            </div>

            <div class="ml-0 lg:ml-64">
                <div class="fixed top-0 left-0 lg:left-64 right-0 h-16 bg-white border-b border-gray-200 z-10 shadow-sm flex items-center justify-between px-4 sm:px-6 lg:px-8">
                    <div class="flex-1 max-w-full hidden sm:block">
                        <div class="relative">
                            <input
                                type="search"
                                placeholder="Buscar lançamentos financeiros..."
                                class="w-full pl-9 pr-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#79B9B0] focus:border-[#79B9B0]"
                            >
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 rounded-full bg-gray-300"></span>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="hidden md:flex items-center gap-2 text-sm">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-[#6c6f7f] text-white text-xs font-medium">
                                {{ strtoupper(mb_substr(auth()->user()->name ?? 'AU', 0, 2)) }}
                            </span>
                            <div class="leading-tight">
                                <p class="text-gray-700 font-medium text-xs md:text-sm">
                                    {{ auth()->user()->name ?? 'Admin User' }}
                                </p>
                                <p class="text-gray-500 text-[11px]">Administrator</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button
                                type="submit"
                                class="text-xs font-medium text-gray-500 hover:text-red-600 hover:underline"
                            >
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </div>
                </div>

                <main class="pt-20 pb-8 px-4 sm:px-6 lg:px-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>

