<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ sidebarOpen: true }">
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
            <!-- Sidebar -->
            <aside
                class="fixed inset-y-0 left-0 z-20 flex w-64 flex-col bg-[#313e50] text-white shadow-lg transition-transform duration-200 lg:translate-x-0"
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            >
                <div class="flex items-center justify-between px-4 py-4 border-b border-white/10">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-[#6c6f7f] flex items-center justify-center">
                            <span class="text-xs font-semibold">ERP</span>
                        </div>
                        <div class="leading-tight">
                            <h1 class="text-sm font-semibold">{{ config('app.name', 'OccHealth ERP') }}</h1>
                            <p class="text-[11px] text-white/70">Occupational Health Clinic</p>
                        </div>
                    </div>
                    <button
                        type="button"
                        class="inline-flex items-center justify-center w-8 h-8 rounded-md hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white/40 lg:hidden"
                        @click="sidebarOpen = false"
                    >
                        <span class="sr-only">Fechar menu</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <nav class="flex-1 px-3 py-4 overflow-y-auto">
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('dashboard') }}"
                               class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('dashboard') ? 'bg-[#6c6f7f] text-white shadow-sm' : 'text-white/80 hover:bg-[#455561] hover:text-white' }}">
                                <span class="inline-flex h-5 w-5 items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l9-9 9 9M5 10v9h4v-5h6v5h4v-9" />
                                    </svg>
                                </span>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('erp.accounts-receivable') }}"
                               class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('erp.accounts-receivable') ? 'bg-[#6c6f7f] text-white shadow-sm' : 'text-white/80 hover:bg-[#455561] hover:text-white' }}">
                                <span class="inline-flex h-5 w-5 items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 19h16M7 11l4 4 6-8" />
                                    </svg>
                                </span>
                                <span>Accounts Receivable</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('erp.accounts-payable') }}"
                               class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('erp.accounts-payable') ? 'bg-[#6c6f7f] text-white shadow-sm' : 'text-white/80 hover:bg-[#455561] hover:text-white' }}">
                                <span class="inline-flex h-5 w-5 items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5h16M7 13l4-4 6 8" />
                                    </svg>
                                </span>
                                <span>Accounts Payable</span>
                            </a>
                        </li>
                    </ul>
                </nav>

                <div class="px-4 py-3 border-t border-white/10 text-center text-[11px] text-white/60">
                    <p>Occupational Health Clinic</p>
                    <p class="mt-1">© {{ date('Y') }} All rights reserved</p>
                </div>
            </aside>

            <!-- Overlay mobile -->
            <div
                class="fixed inset-0 z-10 bg-black/40 backdrop-blur-sm lg:hidden"
                x-show="sidebarOpen"
                x-transition.opacity
                @click="sidebarOpen = false"
            ></div>

            <!-- Main area -->
            <div class="flex min-h-screen flex-col lg:pl-64">
                <!-- Top bar -->
                <header class="sticky top-0 z-10 flex h-16 items-center justify-between border-b border-gray-200 bg-white/90 px-4 backdrop-blur sm:px-6 lg:px-8">
                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            class="inline-flex items-center justify-center rounded-md p-2 text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-[#79B9B0] lg:hidden"
                            @click="sidebarOpen = true"
                        >
                            <span class="sr-only">Abrir menu</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        <div class="hidden sm:block w-full max-w-md">
                            <div class="relative">
                                <input
                                    type="search"
                                    placeholder="Buscar lançamentos financeiros..."
                                    class="w-full rounded-lg border border-gray-200 bg-gray-50 py-2 pl-9 pr-3 text-sm focus:border-[#79B9B0] focus:outline-none focus:ring-2 focus:ring-[#79B9B0]"
                                >
                                <span class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 rounded-full bg-gray-300"></span>
                            </div>
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
                </header>

                <main class="flex-1 px-4 pb-8 pt-6 sm:px-6 lg:px-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>

