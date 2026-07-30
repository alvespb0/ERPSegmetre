<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Comprovante de Pagamento</title>

    <style>
        body{
            font-family: Arial, Helvetica, sans-serif;
            font-size:12px;
            color:#222;
            margin:0;
            padding:25px;
            background:#fff;
        }

        .document{
            border:1px solid #cfcfcf;
        }

        /* =========================
           CABEÇALHO
        ==========================*/

        .header{
            text-align:center;
            padding:22px;
            border-bottom:2px solid #444;
        }

        .header h1{
            margin:0;
            font-size:22px;
            text-transform:uppercase;
            letter-spacing:1px;
        }

        .header .subtitle{
            margin-top:8px;
            color:#666;
            font-size:11px;
        }

        /* =========================
           DESTAQUE VALOR
        ==========================*/

        .highlight-box{
            margin:20px;
            border:1px solid #d9d9d9;
            background:#f8f8f8;
            text-align:center;
            padding:18px;
        }

        .highlight-label{
            font-size:11px;
            color:#666;
            text-transform:uppercase;
            margin-bottom:8px;
        }

        .highlight-value{
            font-size:30px;
            font-weight:bold;
            color:#111;
            margin-bottom:10px;
        }

        .status-success{
            display:inline-block;
            padding:5px 15px;
            border:1px solid #4caf50;
            background:#edf8ed;
            color:#2f6f31;
            font-size:11px;
            font-weight:bold;
            text-transform:uppercase;
        }

        /* =========================
           SEÇÕES
        ==========================*/

        .section-title{
            background:#ececec;
            padding:8px 15px;
            font-size:11px;
            font-weight:bold;
            text-transform:uppercase;
            letter-spacing:.8px;
            border-top:1px solid #d8d8d8;
            border-bottom:1px solid #d8d8d8;
        }

        /* =========================
           TABELAS
        ==========================*/

        .data-table{
            width:100%;
            border-collapse:collapse;
        }

        .data-table td{
            padding:12px 15px;
            vertical-align:top;
            border-bottom:1px solid #ececec;
        }

        .data-table tr:last-child td{
            border-bottom:none;
        }

        .divider{
            border-left:1px solid #ececec;
        }

        .label{
            display:block;
            font-size:9px;
            color:#777;
            text-transform:uppercase;
            margin-bottom:5px;
        }

        .value{
            font-size:13px;
            font-weight:bold;
            color:#111;
        }

        /* =========================
           AUTENTICAÇÃO
        ==========================*/

        .auth-wrapper{
            margin:20px;
            border:1px solid #d8d8d8;
            background:#fafafa;
        }

        .auth-table{
            width:100%;
            border-collapse:collapse;
        }

        .auth-table td{
            padding:15px;
            vertical-align:top;
        }

        .auth-divider{
            border-left:1px solid #dddddd;
        }

        .auth-code{
            display:block;
            margin-top:6px;
            font-family:"Courier New", monospace;
            font-size:12px;
            font-weight:bold;
            word-break:break-all;
        }

        /* =========================
           RODAPÉ
        ==========================*/

        .footer{
            margin-top:10px;
            border-top:1px solid #ddd;
            text-align:center;
            padding:18px;
            font-size:10px;
            color:#777;
            line-height:16px;
        }
    </style>

</head>
<body>

<div class="document">

    <!-- CABEÇALHO -->

    <div class="header">

        <h1>Comprovante de Pagamento</h1>

        <div class="subtitle">
            Transação Nº {{ $retorno['pagamento']['id_pagamento'] ?? 'N/A' }}
            <br>
            Emitido em {{ \Carbon\Carbon::now()->format('d/m/Y \à\s H:i:s') }}
        </div>

    </div>

    <!-- VALOR -->

    <div class="highlight-box">

        <div class="highlight-label">
            Valor Pago
        </div>

        <div class="highlight-value">
            R$ {{ number_format($retorno['pagamento']['valor'] ?? 0, 2, ',', '.') }}
        </div>

        <span class="status-success">
            {{ strtoupper($retorno['status'] ?? 'N/A') }}
        </span>

    </div>

    <!-- DADOS PAGAMENTO -->

    <div class="section-title">
        Dados do Pagamento
    </div>

    <table class="data-table">

        <tr>

            <td width="50%">
                <span class="label">Data de Pagamento</span>
                <span class="value">
                    {{ isset($retorno['pagamento']['data_pagamento']) ? \Carbon\Carbon::parse($retorno['pagamento']['data_pagamento'])->format('d/m/Y') : '-' }}
                </span>
            </td>

            <td width="50%" class="divider">
                <span class="label">Vencimento</span>
                <span class="value">
                    {{ isset($retorno['pagamento']['data_vencimento']) ? \Carbon\Carbon::parse($retorno['pagamento']['data_vencimento'])->format('d/m/Y') : '-' }}
                </span>
            </td>

        </tr>

        @if((isset($retorno['pagamento']['valor_multa']) && $retorno['pagamento']['valor_multa'] > 0) || (isset($retorno['pagamento']['valor_desconto']) && $retorno['pagamento']['valor_desconto'] > 0))

        <tr>

            <td>
                <span class="label">Multa / Juros</span>
                <span class="value">
                    R$ {{ number_format($retorno['pagamento']['valor_multa'] ?? 0,2,',','.') }}
                </span>
            </td>

            <td class="divider">
                <span class="label">Desconto</span>
                <span class="value">
                    R$ {{ number_format($retorno['pagamento']['valor_desconto'] ?? 0,2,',','.') }}
                </span>
            </td>

        </tr>

        @endif

    </table>

    <!-- PAGADOR -->

    <div class="section-title">
        Pagador
    </div>

    <table class="data-table">

        <tr>

            <td width="70%">
                <span class="label">Nome / Razão Social</span>
                <span class="value">
                    {{ $retorno['pagador']['nome'] ?? 'Não informado' }}
                </span>
            </td>

            <td width="30%" class="divider">
                <span class="label">CPF / CNPJ</span>
                <span class="value">
                    {{ $retorno['pagador']['cpf_cnpj'] ?? 'Não informado' }}
                </span>
            </td>

        </tr>

    </table>

    <!-- BENEFICIÁRIO -->

    <div class="section-title">
        Beneficiário
    </div>

    <table class="data-table">

        <tr>

            <td colspan="2">
                <span class="label">Nome / Razão Social</span>
                <span class="value">
                    {{ $retorno['destinatario']['nome'] ?? 'Não informado' }}
                </span>
            </td>

        </tr>

        <tr>

            <td width="50%">
                <span class="label">Banco</span>
                <span class="value">
                    {{ $retorno['destinatario']['banco'] ?? '-' }}
                </span>
            </td>

            <td width="50%" class="divider">
                <span class="label">CPF / CNPJ</span>
                <span class="value">
                    {{ $retorno['destinatario']['cpf_cnpj'] ?? '-' }}
                </span>
            </td>

        </tr>

        <tr>

            <td width="50%">
                <span class="label">Documento</span>
                <span class="value">
                    {{ $retorno['destinatario']['documento'] ?? '-' }}
                </span>
            </td>

            <td width="50%" class="divider">
                <span class="label">Nosso Número</span>
                <span class="value">
                    {{ $retorno['destinatario']['nosso_numero'] ?? '-' }}
                </span>
            </td>

        </tr>

    </table>

    <!-- AUTENTICAÇÃO -->

    <div class="section-title">
        Autenticação
    </div>

    <div class="auth-wrapper">

        <table class="auth-table">

            <tr>

                <td width="50%">

                    <span class="label">
                        Código de Autenticação Bancária
                    </span>

                    <span class="auth-code">
                        {{ $retorno['pagamento']['codigo_autenticacao'] ?? '-' }}
                    </span>

                </td>

                <td width="50%" class="auth-divider">

                    <span class="label">
                        Chave de Idempotência
                    </span>

                    <span class="auth-code" style="font-size:11px;">
                        {{ $retorno['idempotency_key'] ?? '-' }}
                    </span>

                </td>

            </tr>

        </table>

    </div>

    <!-- RODAPÉ -->

    <div class="footer">

        Este comprovante foi emitido eletronicamente pelo sistema financeiro.

        <br><br>

        A autenticidade desta operação pode ser validada através do código de autenticação bancária informado neste documento.

    </div>

</div>

</body>
</html>