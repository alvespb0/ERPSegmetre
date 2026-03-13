<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-background text-foreground">
        <div class="min-h-screen flex items-stretch bg-[var(--erp-background)]">
            <!-- Painel lateral / branding -->
            <div class="hidden lg:flex lg:flex-col lg:w-1/2 xl:w-2/5 bg-[#313e50] text-white px-12 py-10 justify-between">
                <div>
                    <a href="/" class="inline-flex items-center gap-3">
                        <x-application-logo class="w-10 h-10 text-white" />
                        <span class="text-lg font-semibold tracking-tight">
                            {{ config('app.name', 'ERP Clinic') }}
                        </span>
                    </a>

                    <div class="mt-10 space-y-4">
                        <h1 class="text-3xl font-semibold leading-tight">
                            Gestão completa para sua clínica em um só lugar.
                        </h1>
                        <p class="text-sm text-gray-200/80 max-w-md">
                            Acompanhe indicadores financeiros, fluxo de caixa, contas a pagar e receber com a mesma
                            experiência visual do seu novo dashboard ERP.
                        </p>
                    </div>
                </div>

                <div class="space-y-2 text-xs text-gray-200/70">
                    <p>Painéis claros, gráficos financeiros e visão consolidada da sua operação.</p>
                    <p>Produzido a partir do layout “ERP Dashboard for Clinic”.</p>
                </div>
            </div>

            <!-- Área do formulário -->
            <div class="flex-1 flex items-center justify-center px-6 py-10 sm:px-8 lg:px-12">
                <div class="w-full max-w-md">
                    <div class="mb-8 flex items-center justify-between lg:hidden">
                        <a href="/" class="inline-flex items-center gap-3">
                            <x-application-logo class="w-8 h-8 text-[#313e50]" />
                            <span class="text-base font-semibold text-[#313e50]">
                                {{ config('app.name', 'ERP Clinic') }}
                            </span>
                        </a>
                    </div>

                    <div class="bg-card border border-[var(--erp-card-border)] shadow-sm rounded-2xl px-6 py-8 sm:px-8 sm:py-9">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
