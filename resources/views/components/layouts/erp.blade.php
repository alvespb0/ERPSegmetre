<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ sidebarOpen: true }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'MediGest') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:300,400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-800">
        <div class="min-h-screen bg-[var(--erp-background)]">
            
            <aside
                class="fixed inset-y-0 left-0 z-20 flex w-64 flex-col bg-[#2C394B] text-white shadow-xl transition-transform duration-300 lg:translate-x-0"
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            >
                <div class="flex items-center justify-between px-5 py-5 border-b border-white/5">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-white/10 flex items-center justify-center shadow-sm">
                            <span class="text-xs font-bold tracking-wider text-white/90">ERP</span>
                        </div>
                        <div class="leading-none">
                            <h1 class="text-sm font-semibold tracking-tight text-white/90">{{ config('app.name', 'MediGest') }}</h1>
                            <p class="text-[10px] text-white/50 mt-1 uppercase tracking-wide">MediGest</p>
                        </div>
                    </div>
                    <button
                        type="button"
                        class="inline-flex items-center justify-center w-8 h-8 rounded-md text-white/50 hover:bg-white/10 hover:text-white transition-colors lg:hidden"
                        @click="sidebarOpen = false"
                    >
                        <span class="sr-only">Fechar menu</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <nav class="flex-1 px-3 py-6 overflow-y-auto [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-thumb]:bg-white/10 [&::-webkit-scrollbar-thumb]:rounded-full hover:[&::-webkit-scrollbar-thumb]:bg-white/20">
                    <ul class="space-y-1">
                        
                        <li>
                            <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-white/10 text-white font-medium' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                                <span class="inline-flex h-5 w-5 items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l9-9 9 9M5 10v9h4v-5h6v5h4v-9" />
                                    </svg>
                                </span>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <li class="pt-4 pb-1" x-data="{ openCat: {{ request()->routeIs('erp.entidades.*') || request()->routeIs('erp.centro-custo.*') || request()->routeIs('erp.categoria-financeira.*') || request()->routeIs('erp.forma-pagamento.*') || request()->routeIs('erp.banco.*') || request()->routeIs('erp.tipo-conta.*') || request()->routeIs('erp.conta.*') || request()->routeIs('erp.usuarios.*') ? 'true' : 'false' }} }">
                            <div class="px-3 mb-2">
                                <span class="text-[10px] font-semibold tracking-widest text-white/40 uppercase">Cadastros</span>
                            </div>
                            
                            <ul class="mt-1 space-y-1">
                                <li x-data="{ openSub: {{ request()->routeIs('erp.entidades.*') ? 'true' : 'false' }} }">
                                    <button @click="openSub = !openSub" class="flex w-full items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-sm transition-all duration-200 text-white/60 hover:bg-white/5 hover:text-white">
                                        <div class="flex items-center gap-3">
                                            <span class="inline-flex h-5 w-5 items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                                            </span>
                                            <span>Entidades</span>
                                        </div>
                                        <svg :class="openSub ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3 w-3 opacity-50 transition-transform duration-200"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                                    </button>
                                    <ul x-show="openSub" x-collapse class="mt-1 space-y-1 pl-11">
                                        <li>
                                            <a href="{{ route('erp.entidades.create') }}" class="block rounded-lg px-3 py-2 text-sm transition-all duration-200 {{ request()->routeIs('erp.entidades.create') ? 'text-white font-medium bg-white/10' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">Nova Entidade</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('erp.entidades.index') }}" class="block rounded-lg px-3 py-2 text-sm transition-all duration-200 {{ request()->routeIs('erp.entidades.index') ? 'text-white font-medium bg-white/10' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">Listar Entidades</a>
                                        </li>
                                    </ul>
                                </li>

                                <li x-data="{ openSub: {{ request()->routeIs('erp.centro-custo.*') ? 'true' : 'false' }} }">
                                    <button @click="openSub = !openSub" class="flex w-full items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-sm transition-all duration-200 text-white/60 hover:bg-white/5 hover:text-white">
                                        <div class="flex items-center gap-3">
                                            <span class="inline-flex h-5 w-5 items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" /></svg>
                                            </span>
                                            <span>Centro de Custo</span>
                                        </div>
                                        <svg :class="openSub ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3 w-3 opacity-50 transition-transform duration-200"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                                    </button>
                                    <ul x-show="openSub" x-collapse class="mt-1 space-y-1 pl-11">
                                        <li>
                                            <a href="{{route('erp.centro-custo.create')}}" class="block rounded-lg px-3 py-2 text-sm transition-all duration-200 {{ request()->routeIs('erp.centro-custo.create') ? 'text-white font-medium bg-white/10' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">Novo Centro de Custo</a>
                                        </li>
                                        <li>
                                            <a href="{{route('erp.centro-custo.index')}}" class="block rounded-lg px-3 py-2 text-sm transition-all duration-200 {{ request()->routeIs('erp.centro-custo.index') ? 'text-white font-medium bg-white/10' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">Listar Centros de Custo</a>
                                        </li>
                                    </ul>
                                </li>

                                <li x-data="{ openSub: {{ request()->routeIs('erp.categoria-financeira.*') ? 'true' : 'false' }} }">
                                    <button @click="openSub = !openSub" class="flex w-full items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-sm transition-all duration-200 text-white/60 hover:bg-white/5 hover:text-white">
                                        <div class="flex items-center gap-3">
                                            <span class="inline-flex h-5 w-5 items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" /></svg>
                                            </span>
                                            <span>Categoria Financeira</span>
                                        </div>
                                        <svg :class="openSub ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3 w-3 opacity-50 transition-transform duration-200"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                                    </button>
                                    <ul x-show="openSub" x-collapse class="mt-1 space-y-1 pl-11">
                                        <li>
                                            <a href="{{route('erp.categoria-financeira.create')}}" class="block rounded-lg px-3 py-2 text-sm transition-all duration-200 {{ request()->routeIs('erp.categoria-financeira.create') ? 'text-white font-medium bg-white/10' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">Nova Categoria</a>
                                        </li>
                                        <li>
                                            <a href="#" class="block rounded-lg px-3 py-2 text-sm transition-all duration-200 {{ request()->routeIs('erp.categoria-financeira.index') ? 'text-white font-medium bg-white/10' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">Listar Categorias</a>
                                        </li>
                                    </ul>
                                </li>

                                <li x-data="{ openSub: {{ request()->routeIs('erp.forma-pagamento.*') ? 'true' : 'false' }} }">
                                    <button @click="openSub = !openSub" class="flex w-full items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-sm transition-all duration-200 text-white/60 hover:bg-white/5 hover:text-white">
                                        <div class="flex items-center gap-3">
                                            <span class="inline-flex h-5 w-5 items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" /></svg>
                                            </span>
                                            <span>Forma de Pagamento</span>
                                        </div>
                                        <svg :class="openSub ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3 w-3 opacity-50 transition-transform duration-200"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                                    </button>
                                    <ul x-show="openSub" x-collapse class="mt-1 space-y-1 pl-11">
                                        <li>
                                            <a href="#" class="block rounded-lg px-3 py-2 text-sm transition-all duration-200 {{ request()->routeIs('erp.forma-pagamento.create') ? 'text-white font-medium bg-white/10' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">Nova Forma de Pag.</a>
                                        </li>
                                        <li>
                                            <a href="#" class="block rounded-lg px-3 py-2 text-sm transition-all duration-200 {{ request()->routeIs('erp.forma-pagamento.index') ? 'text-white font-medium bg-white/10' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">Listar Formas de Pag.</a>
                                        </li>
                                    </ul>
                                </li>

                                <li x-data="{ openSub: {{ request()->routeIs('erp.banco.*') ? 'true' : 'false' }} }">
                                    <button @click="openSub = !openSub" class="flex w-full items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-sm transition-all duration-200 text-white/60 hover:bg-white/5 hover:text-white">
                                        <div class="flex items-center gap-3">
                                            <span class="inline-flex h-5 w-5 items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" /></svg>
                                            </span>
                                            <span>Banco</span>
                                        </div>
                                        <svg :class="openSub ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3 w-3 opacity-50 transition-transform duration-200"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                                    </button>
                                    <ul x-show="openSub" x-collapse class="mt-1 space-y-1 pl-11">
                                        <li>
                                            <a href="#" class="block rounded-lg px-3 py-2 text-sm transition-all duration-200 {{ request()->routeIs('erp.banco.create') ? 'text-white font-medium bg-white/10' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">Novo Banco</a>
                                        </li>
                                        <li>
                                            <a href="#" class="block rounded-lg px-3 py-2 text-sm transition-all duration-200 {{ request()->routeIs('erp.banco.index') ? 'text-white font-medium bg-white/10' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">Listar Bancos</a>
                                        </li>
                                    </ul>
                                </li>

                                <li x-data="{ openSub: {{ request()->routeIs('erp.tipo-conta.*') ? 'true' : 'false' }} }">
                                    <button @click="openSub = !openSub" class="flex w-full items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-sm transition-all duration-200 text-white/60 hover:bg-white/5 hover:text-white">
                                        <div class="flex items-center gap-3">
                                            <span class="inline-flex h-5 w-5 items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25z" /></svg>
                                            </span>
                                            <span>Tipo de Conta</span>
                                        </div>
                                        <svg :class="openSub ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3 w-3 opacity-50 transition-transform duration-200"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                                    </button>
                                    <ul x-show="openSub" x-collapse class="mt-1 space-y-1 pl-11">
                                        <li>
                                            <a href="#" class="block rounded-lg px-3 py-2 text-sm transition-all duration-200 {{ request()->routeIs('erp.tipo-conta.create') ? 'text-white font-medium bg-white/10' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">Novo Tipo de Conta</a>
                                        </li>
                                        <li>
                                            <a href="#" class="block rounded-lg px-3 py-2 text-sm transition-all duration-200 {{ request()->routeIs('erp.tipo-conta.index') ? 'text-white font-medium bg-white/10' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">Listar Tipos de Conta</a>
                                        </li>
                                    </ul>
                                </li>

                                <li x-data="{ openSub: {{ request()->routeIs('erp.conta.*') ? 'true' : 'false' }} }">
                                    <button @click="openSub = !openSub" class="flex w-full items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-sm transition-all duration-200 text-white/60 hover:bg-white/5 hover:text-white">
                                        <div class="flex items-center gap-3">
                                            <span class="inline-flex h-5 w-5 items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3" /></svg>
                                            </span>
                                            <span>Conta</span>
                                        </div>
                                        <svg :class="openSub ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3 w-3 opacity-50 transition-transform duration-200"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                                    </button>
                                    <ul x-show="openSub" x-collapse class="mt-1 space-y-1 pl-11">
                                        <li>
                                            <a href="#" class="block rounded-lg px-3 py-2 text-sm transition-all duration-200 {{ request()->routeIs('erp.conta.create') ? 'text-white font-medium bg-white/10' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">Nova Conta</a>
                                        </li>
                                        <li>
                                            <a href="#" class="block rounded-lg px-3 py-2 text-sm transition-all duration-200 {{ request()->routeIs('erp.conta.index') ? 'text-white font-medium bg-white/10' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">Listar Contas</a>
                                        </li>
                                    </ul>
                                </li>

                                <li x-data="{ openSub: {{ request()->routeIs('erp.usuarios.*') ? 'true' : 'false' }} }">
                                    <button @click="openSub = !openSub" class="flex w-full items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-sm transition-all duration-200 text-white/60 hover:bg-white/5 hover:text-white">
                                        <div class="flex items-center gap-3">
                                            <span class="inline-flex h-5 w-5 items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                                            </span>
                                            <span>Usuários</span>
                                        </div>
                                        <svg :class="openSub ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3 w-3 opacity-50 transition-transform duration-200"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                                    </button>
                                    <ul x-show="openSub" x-collapse class="mt-1 space-y-1 pl-11">
                                        <li>
                                            <a href="#" class="block rounded-lg px-3 py-2 text-sm transition-all duration-200 {{ request()->routeIs('erp.usuarios.create') ? 'text-white font-medium bg-white/10' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">Novo Usuário</a>
                                        </li>
                                        <li>
                                            <a href="#" class="block rounded-lg px-3 py-2 text-sm transition-all duration-200 {{ request()->routeIs('erp.usuarios.index') ? 'text-white font-medium bg-white/10' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">Listar Usuários</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>

                        <li class="pt-4 pb-1" x-data="{ openCat: {{ request()->routeIs('erp.financeiro.*') ? 'true' : 'false' }} }">
                            <div class="px-3 mb-2">
                                <span class="text-[10px] font-semibold tracking-widest text-white/40 uppercase">Financeiro</span>
                            </div>
                            
                            <ul class="mt-1 space-y-1">
                                <li>
                                    <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('erp.contas-receber.*') ? 'bg-white/10 text-white font-medium' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                                        <span class="inline-flex h-5 w-5 items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 19h16M7 11l4 4 6-8" /></svg>
                                        </span>
                                        <span>Contas a Receber</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('erp.contas-pagar.*') ? 'bg-white/10 text-white font-medium' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                                        <span class="inline-flex h-5 w-5 items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 5h16M7 13l4-4 6 8" /></svg>
                                        </span>
                                        <span>Contas a Pagar</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('erp.titulos.*') ? 'bg-white/10 text-white font-medium' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                                        <span class="inline-flex h-5 w-5 items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                                        </span>
                                        <span>Lançamento de Títulos</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="pt-4 pb-1" x-data="{ openCat: {{ request()->routeIs('erp.administracao.*') ? 'true' : 'false' }} }">
                            <div class="px-3 mb-2">
                                <span class="text-[10px] font-semibold tracking-widest text-white/40 uppercase">Administração</span>
                            </div>
                            
                            <ul class="mt-1 space-y-1">

                                <li>
                                    <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('erp.relatorios.*') ? 'bg-white/10 text-white font-medium' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                                        <span class="inline-flex h-5 w-5 items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" /><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" /></svg>
                                        </span>
                                        <span>Relatórios</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('erp.manual.*') ? 'bg-white/10 text-white font-medium' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                                        <span class="inline-flex h-5 w-5 items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                                        </span>
                                        <span>Manual</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="pt-4 pb-1" x-data="{ openCat: {{ request()->routeIs('erp.dev.*') ? 'true' : 'false' }} }">
                            <div class="px-3 mb-2">
                                <span class="text-[10px] font-semibold tracking-widest text-white/40 uppercase">Dev</span>
                            </div>
                            
                            <ul class="mt-1 space-y-1">
                                <li>
                                    <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('erp.integracoes.*') ? 'bg-white/10 text-white font-medium' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                                        <span class="inline-flex h-5 w-5 items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M14.25 9.75L16.5 12l-2.25 2.25m-4.5 0L7.5 12l2.25-2.25M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z" /></svg>
                                        </span>
                                        <span>Integrações</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                    </ul>
                </nav>
                
                <div class="px-5 py-4 border-t border-white/5 text-center">
                    <p class="text-[10px] text-white/40 tracking-wider">© {{ date('Y') }} ALL RIGHTS RESERVED</p>
                </div>
            </aside>

            <div
                class="fixed inset-0 z-10 bg-gray-900/50 backdrop-blur-sm transition-opacity lg:hidden"
                x-show="sidebarOpen"
                x-transition.opacity
                @click="sidebarOpen = false"
            ></div>

            <div class="flex min-h-screen flex-col lg:pl-64">
                
                <header class="sticky top-0 z-10 flex h-16 items-center justify-between border-b border-gray-200/80 bg-white/80 px-4 backdrop-blur-md sm:px-6 lg:px-8">
                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 focus:outline-none transition-colors lg:hidden"
                            @click="sidebarOpen = true"
                        >
                            <span class="sr-only">Abrir menu</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        <div class="hidden sm:block w-full max-w-md">
                            <div class="relative">
                                <input
                                    type="search"
                                    placeholder="Buscar lançamentos financeiros..."
                                    class="w-full rounded-full border border-gray-200 bg-gray-50/50 py-2 pl-10 pr-4 text-sm text-gray-700 placeholder-gray-400 focus:border-[#2C394B] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#2C394B] transition-all"
                                >
                                <span class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-6">
                        <div class="hidden md:flex items-center gap-3 text-sm">
                            <div class="leading-tight text-right">
                                <p class="text-gray-700 font-medium text-xs md:text-sm">
                                    {{ auth()->user()->name ?? 'Admin User' }}
                                </p>
                                <p class="text-gray-400 text-[11px] font-medium tracking-wide uppercase">Administrator</p>
                            </div>
                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-[#2C394B] text-white text-xs font-semibold shadow-sm">
                                {{ strtoupper(mb_substr(auth()->user()->name ?? 'AU', 0, 2)) }}
                            </span>
                        </div>

                        <div class="h-5 w-[1px] bg-gray-200 hidden md:block"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button
                                type="submit"
                                class="text-sm font-medium text-gray-500 hover:text-red-600 transition-colors"
                            >
                                {{ __('Sair') }}
                            </button>
                        </form>
                    </div>
                </header>

                <main class="flex-1 px-4 pb-8 pt-6 sm:px-6 lg:px-8">
                    {{ $slot }}

                    {{-- Toast container --}}
                    <div
                        x-data="{
                            toasts: [],
                            add(type, message) {
                                if (!message) return;
                                this.toasts.push({ id: Date.now() + Math.random(), type, message });
                            }
                        }"
                        x-init="
                            @if(session('toast-message'))
                                add('message', '{{ addslashes(session('toast-message')) }}');
                            @endif
                            @if(session('toast-error'))
                                add('error', '{{ addslashes(session('toast-error')) }}');
                            @endif

                            window.addEventListener('toast-message', e => {
                                const detail = e.detail;
                                const msg = typeof detail === 'string'
                                    ? detail
                                    : (detail?.message ?? (Array.isArray(detail) ? detail[0] : ''));
                                add('message', msg);
                            });

                            window.addEventListener('toast-error', e => {
                                const detail = e.detail;
                                const msg = typeof detail === 'string'
                                    ? detail
                                    : (detail?.message ?? (Array.isArray(detail) ? detail[0] : ''));
                                add('error', msg);
                            });
                        "
                        class="pointer-events-none fixed inset-0 z-50 flex items-start justify-end px-4 py-6 sm:p-6"
                    >
                        <div class="flex w-full flex-col items-end space-y-3">
                            <template x-for="toast in toasts" :key="toast.id">
                                <div
                                    x-data="{ open: true }"
                                    x-show="open"
                                    x-transition.opacity.duration.150ms
                                    x-transition.scale.duration.150ms
                                    x-init="setTimeout(() => open = false, 4000)"
                                    class="pointer-events-auto w-full max-w-sm rounded-xl shadow-lg border px-4 py-3 flex items-start gap-3 text-sm"
                                    :class="toast.type === 'error'
                                        ? 'bg-red-50 border-red-200 text-red-800'
                                        : 'bg-emerald-50 border-emerald-200 text-emerald-800'"
                                >
                                    <div class="mt-0.5">
                                        <span
                                            class="inline-flex h-6 w-6 items-center justify-center rounded-full"
                                            :class="toast.type === 'error'
                                                ? 'bg-red-100 text-red-700'
                                                : 'bg-emerald-100 text-emerald-700'"
                                        >
                                            <template x-if="toast.type === 'error'">
                                                <span>!</span>
                                            </template>
                                            <template x-if="toast.type !== 'error'">
                                                <span>✓</span>
                                            </template>
                                        </span>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium" x-text="toast.message"></p>
                                    </div>
                                    <button
                                        type="button"
                                        class="ml-2 text-xs text-gray-400 hover:text-gray-600"
                                        @click="open = false"
                                    >
                                        fechar
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </main>
            </div>
        </div>
        @livewireScripts
    </body>
</html>