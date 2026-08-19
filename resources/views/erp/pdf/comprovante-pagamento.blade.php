<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">

    <title>Comprovante de Pagamento</title>

    <style>

        @page {
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 35px;
            background: #ffffff;
            color: #222222;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
        }

        .page {
            width: 100%;
        }

        /* =====================================================
           CABEÇALHO
        ====================================================== */

        .header {
            width: 100%;
            padding-bottom: 22px;
            border-bottom: 1px solid #dcdcdc;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-left {
            width: 65%;
            vertical-align: middle;
        }

        .header-right {
            width: 35%;
            text-align: right;
            vertical-align: middle;
        }

        .brand {
            font-size: 16px;
            font-weight: bold;
            letter-spacing: .3px;
            color: #111111;
        }

        .document-title {
            margin-top: 5px;
            font-size: 10px;
            color: #777777;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .payment-type {
            display: inline-block;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 1px;
            color: #555555;
            border: 1px solid #cccccc;
            padding: 5px 9px;
        }


        /* =====================================================
           RESUMO
        ====================================================== */

        .summary {
            padding: 28px 0 25px 0;
            border-bottom: 1px solid #e1e1e1;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-value {
            font-size: 28px;
            font-weight: bold;
            color: #111111;
            line-height: 32px;
        }

        .summary-label {
            margin-bottom: 6px;
            font-size: 9px;
            color: #888888;
            text-transform: uppercase;
            letter-spacing: .8px;
        }

        .summary-status {
            text-align: right;
            vertical-align: bottom;
        }

        .status {
            font-size: 10px;
            font-weight: bold;
            color: #333333;
            letter-spacing: .6px;
        }

        .status:before {
            content: "✓";
            display: inline-block;
            margin-right: 5px;
            font-size: 11px;
        }


        /* =====================================================
           SEÇÕES
        ====================================================== */

        .section {
            margin-top: 25px;
        }

        .section-header {
            width: 100%;
            border-bottom: 1px solid #d7d7d7;
            padding-bottom: 7px;
            margin-bottom: 0;
        }

        .section-title {
            font-size: 10px;
            font-weight: bold;
            color: #444444;
            text-transform: uppercase;
            letter-spacing: 1px;
        }


        /* =====================================================
           DADOS
        ====================================================== */

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table td {
            width: 50%;
            padding: 13px 0;
            vertical-align: top;
            border-bottom: 1px solid #eeeeee;
        }

        .data-table td + td {
            padding-left: 25px;
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .field-label {
            display: block;
            margin-bottom: 4px;
            color: #888888;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: .6px;
        }

        .field-value {
            display: block;
            color: #222222;
            font-size: 11px;
            font-weight: bold;
            line-height: 15px;
        }

        .field-value.normal {
            font-weight: normal;
        }


        /* =====================================================
           DESTAQUE DE IDENTIFICADOR
        ====================================================== */

        .identifier {
            margin-top: 10px;
            padding: 14px 0;
            border-bottom: 1px solid #eeeeee;
        }

        .identifier-label {
            font-size: 8px;
            color: #888888;
            text-transform: uppercase;
            letter-spacing: .6px;
            margin-bottom: 5px;
        }

        .identifier-value {
            font-family: "Courier New", monospace;
            font-size: 10px;
            color: #222222;
            word-break: break-all;
        }


        /* =====================================================
           BOX DE AUTENTICAÇÃO
        ====================================================== */

        .authentication {
            margin-top: 25px;
            padding: 16px;
            background: #f7f7f7;
            border-left: 3px solid #444444;
        }

        .authentication-table {
            width: 100%;
            border-collapse: collapse;
        }

        .authentication td {
            width: 50%;
            vertical-align: top;
        }

        .authentication td + td {
            padding-left: 25px;
            border-left: 1px solid #dddddd;
        }

        .auth-label {
            font-size: 8px;
            color: #888888;
            text-transform: uppercase;
            letter-spacing: .6px;
            margin-bottom: 6px;
        }

        .auth-value {
            font-family: "Courier New", monospace;
            font-size: 9px;
            color: #222222;
            word-break: break-all;
            line-height: 13px;
        }


        /* =====================================================
           RODAPÉ
        ====================================================== */

        .footer {
            margin-top: 35px;
            padding-top: 15px;
            border-top: 1px solid #dddddd;
            color: #999999;
            font-size: 8px;
            line-height: 13px;
            text-align: center;
        }

        .footer strong {
            color: #777777;
        }

    </style>
</head>

<body>

@php

    /*
    |--------------------------------------------------------------------------
    | Dados
    |--------------------------------------------------------------------------
    */

    $pagamento = $retorno['pagamento'] ?? [];
    $pagador = $retorno['pagador'] ?? [];
    $destinatario = $retorno['destinatario'] ?? [];

    /*
    |--------------------------------------------------------------------------
    | Tipo
    |--------------------------------------------------------------------------
    |
    | Se futuramente você adicionar "tipo" ao retorno, pode trocar
    | essa identificação por:
    |
    | $isPix = ($retorno['tipo'] ?? null) === 'pix';
    |
    */

    $isPix = !empty($retorno['endToEndId']);

    $tipoPagamento = $isPix ? 'PIX' : 'BOLETO';

    /*
    |--------------------------------------------------------------------------
    | Identificador principal
    |--------------------------------------------------------------------------
    */

    $identificador = $isPix
        ? ($retorno['endToEndId'] ?? null)
        : ($pagamento['id_pagamento'] ?? null);

@endphp


<div class="page">

    {{-- =========================================================
         CABEÇALHO
    ========================================================== --}}

    <div class="header">

        <table class="header-table">

            <tr>

                <td class="header-left">

                    <div class="brand">
                        Comprovante de Pagamento
                    </div>

                    <div class="document-title">
                        Documento eletrônico de transação financeira
                    </div>

                </td>


                <td class="header-right">

                    <span class="payment-type">
                        {{ $tipoPagamento }}
                    </span>

                </td>

            </tr>

        </table>

    </div>


    {{-- =========================================================
         RESUMO DO PAGAMENTO
    ========================================================== --}}

    <div class="summary">

        <table class="summary-table">

            <tr>

                <td width="65%">

                    <div class="summary-label">
                        Valor pago
                    </div>

                    <div class="summary-value">
                        R$
                        {{ number_format($pagamento['valor'] ?? 0, 2, ',', '.') }}
                    </div>

                </td>


                <td width="35%" class="summary-status">

                    <div class="summary-label">
                        Situação
                    </div>

                    <div class="status">
                        {{ strtoupper($retorno['status'] ?? 'NÃO INFORMADO') }}
                    </div>

                </td>

            </tr>

        </table>

    </div>


    {{-- =========================================================
         DADOS DO PAGAMENTO
    ========================================================== --}}

    <div class="section">

        <div class="section-header">
            <span class="section-title">
                Dados do pagamento
            </span>
        </div>


        <table class="data-table">

            <tr>

                <td>

                    <span class="field-label">
                        Data do pagamento
                    </span>

                    <span class="field-value">

                        @if(!empty($pagamento['data_pagamento']))
                            {{ \Carbon\Carbon::parse($pagamento['data_pagamento'])->format('d/m/Y H:i:s') }}
                        @else
                            -
                        @endif

                    </span>

                </td>


                @if($isPix)

                    <td>

                        <span class="field-label">
                            Identificador da transação
                        </span>

                        <span class="field-value normal">
                            {{ $retorno['endToEndId'] ?? '-' }}
                        </span>

                    </td>

                @else

                    <td>

                        <span class="field-label">
                            Data de vencimento
                        </span>

                        <span class="field-value">

                            @if(!empty($pagamento['data_vencimento']))
                                {{ \Carbon\Carbon::parse($pagamento['data_vencimento'])->format('d/m/Y') }}
                            @else
                                -
                            @endif

                        </span>

                    </td>

                @endif

            </tr>


            @if(!$isPix)

                <tr>

                    <td>

                        <span class="field-label">
                            Valor original
                        </span>

                        <span class="field-value">
                            R$
                            {{ number_format($pagamento['valor_boleto'] ?? $pagamento['valor'] ?? 0, 2, ',', '.') }}
                        </span>

                    </td>


                    <td>

                        <span class="field-label">
                            Valor pago
                        </span>

                        <span class="field-value">
                            R$
                            {{ number_format($pagamento['valor'] ?? 0, 2, ',', '.') }}
                        </span>

                    </td>

                </tr>


                @if(
                    ($pagamento['valor_desconto'] ?? 0) > 0 ||
                    ($pagamento['valor_multa'] ?? 0) > 0
                )

                    <tr>

                        <td>

                            <span class="field-label">
                                Desconto
                            </span>

                            <span class="field-value">
                                R$
                                {{ number_format($pagamento['valor_desconto'] ?? 0, 2, ',', '.') }}
                            </span>

                        </td>


                        <td>

                            <span class="field-label">
                                Multa / juros
                            </span>

                            <span class="field-value">
                                R$
                                {{ number_format($pagamento['valor_multa'] ?? 0, 2, ',', '.') }}
                            </span>

                        </td>

                    </tr>

                @endif

            @endif

        </table>

    </div>


    {{-- =========================================================
         PAGADOR
    ========================================================== --}}

    <div class="section">

        <div class="section-header">

            <span class="section-title">
                Pagador
            </span>

        </div>


        <table class="data-table">

            <tr>

                <td>

                    <span class="field-label">
                        Nome / Razão social
                    </span>

                    <span class="field-value">
                        {{ $pagador['nome'] ?? 'Não informado' }}
                    </span>

                </td>


                <td>

                    <span class="field-label">
                        CPF / CNPJ
                    </span>

                    <span class="field-value">
                        {{ $pagador['cpf_cnpj'] ?? 'Não informado' }}
                    </span>

                </td>

            </tr>


            @if($isPix)

                <tr>

                    <td>

                        <span class="field-label">
                            Agência
                        </span>

                        <span class="field-value">
                            {{ $pagador['agencia'] ?? '-' }}
                        </span>

                    </td>


                    <td>

                        <span class="field-label">
                            Conta
                        </span>

                        <span class="field-value">
                            {{ $pagador['conta'] ?? '-' }}
                        </span>

                    </td>

                </tr>


                <tr>

                    <td colspan="2">

                        <span class="field-label">
                            Tipo de conta
                        </span>

                        <span class="field-value">
                            {{ $pagador['tipo_conta'] ?? '-' }}
                        </span>

                    </td>

                </tr>

            @endif

        </table>

    </div>


    {{-- =========================================================
         DESTINATÁRIO
    ========================================================== --}}

    <div class="section">

        <div class="section-header">

            <span class="section-title">
                {{ $isPix ? 'Destinatário' : 'Beneficiário' }}
            </span>

        </div>


        <table class="data-table">

            <tr>

                <td>

                    <span class="field-label">
                        Nome / Razão social
                    </span>

                    <span class="field-value">
                        {{ $destinatario['nome'] ?? 'Não informado' }}
                    </span>

                </td>


                <td>

                    <span class="field-label">
                        CPF / CNPJ
                    </span>

                    <span class="field-value">
                        {{ $destinatario['cpf_cnpj'] ?? '-' }}
                    </span>

                </td>

            </tr>


            @if($isPix)

                <tr>

                    <td>

                        <span class="field-label">
                            Agência
                        </span>

                        <span class="field-value">
                            {{ $destinatario['agencia'] ?? '-' }}
                        </span>

                    </td>


                    <td>

                        <span class="field-label">
                            Conta
                        </span>

                        <span class="field-value">
                            {{ $destinatario['conta'] ?? '-' }}
                        </span>

                    </td>

                </tr>


                <tr>

                    <td colspan="2">

                        <span class="field-label">
                            Tipo de conta
                        </span>

                        <span class="field-value">
                            {{ $destinatario['tipo_conta'] ?? '-' }}
                        </span>

                    </td>

                </tr>

            @else

                <tr>

                    <td>

                        <span class="field-label">
                            Instituição emissora
                        </span>

                        <span class="field-value">
                            {{ $destinatario['banco'] ?? '-' }}
                        </span>

                    </td>


                    <td>

                        <span class="field-label">
                            Documento
                        </span>

                        <span class="field-value">
                            {{ $destinatario['documento'] ?? '-' }}
                        </span>

                    </td>

                </tr>


                <tr>

                    <td colspan="2">

                        <span class="field-label">
                            Nosso número
                        </span>

                        <span class="field-value">
                            {{ $destinatario['nosso_numero'] ?? '-' }}
                        </span>

                    </td>

                </tr>

            @endif

        </table>

    </div>


    {{-- =========================================================
         IDENTIFICAÇÃO / AUTENTICAÇÃO
    ========================================================== --}}

    <div class="section">

        <div class="section-header">

            <span class="section-title">

                {{ $isPix ? 'Identificação da transação' : 'Autenticação' }}

            </span>

        </div>


        <div class="authentication">

            <table class="authentication-table">

                <tr>

                    @if($isPix)

                        <td width="100%">

                            <div class="auth-label">
                                End-to-End ID
                            </div>

                            <div class="auth-value">
                                {{ $retorno['endToEndId'] ?? '-' }}
                            </div>

                        </td>

                    @else

                        <td width="50%">

                            <div class="auth-label">
                                Código de autenticação
                            </div>

                            <div class="auth-value">
                                {{ $pagamento['codigo_autenticacao'] ?? '-' }}
                            </div>

                        </td>


                        <td width="50%">

                            <div class="auth-label">
                                Chave de idempotência
                            </div>

                            <div class="auth-value">
                                {{ $retorno['idempotency_key'] ?? '-' }}
                            </div>

                        </td>

                    @endif

                </tr>

            </table>

        </div>

    </div>


    {{-- =========================================================
         RODAPÉ
    ========================================================== --}}

    <div class="footer">

        <strong>Comprovante eletrônico de pagamento</strong>

        <br>

        Documento gerado automaticamente pelo sistema financeiro.

        <br>

        Emitido em {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}.

    </div>

</div>

</body>
</html>
