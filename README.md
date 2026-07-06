
# ERP Segmetre

Sistema de Gestão de Recursos Empresariais (ERP) desenvolvido em Laravel, com foco na gestão financeira robusta, controle de tesouraria, contas a pagar e receber, emissão de cobranças e faturamento bancário. A interface é construída de forma reativa com o Laravel Livewire, proporcionando uma experiência fluida de SPA (Single Page Application).


## Tecnologias Utilizadas

* Framework: Laravel (PHP)
* Frontend: Laravel Livewire, Tailwind CSS, Alpine.js, Vite
* Banco de Dados: MySQL / PostgreSQL (via Eloquent ORM)
* Autenticação: Laravel Auth integrado a módulo Google Authenticator (MFA)


## Arquitetura e Padrões de Projeto

O projeto adota uma separação rigorosa de responsabilidades para garantir que as regras de negócio permaneçam isoladas, testáveis e extensíveis.

### 1. Multi-Tenant por Isolamento de Base (Banco Único por Unidade)
O ERP foi estruturado sob um modelo de negócio Multi-Tenant flexível:
* Cada cliente possui sua própria infraestrutura de banco de dados isolada.
* O sistema permite a expansão nativa para múltiplos ambientes ou unidades sob o mesmo cliente, possibilitando que uma única organização gerencie mais de uma base de dados distinta a partir do seu ecossistema.

### 2. Service Pattern (`app/Services/`)
Toda a lógica de negócios pesada é encapsulada na camada de Serviços, desacoplando os controladores e componentes Livewire.
* Exemplos: `TituloFinanceiroService`, `ParcelaService`, `MovimentacaoService`, `CertificadoDigitalService`.
* Vantagem: Centralização de cálculos financeiros e regras de validação, facilitando o reaproveitamento de código em comandos de console (Artisan) ou Jobs assíncronos.

### 3. Factory Pattern (`app/Factories/`)
* `IntegracaoFactory.php`: Centraliza a instanciação dinâmica dos drivers de comunicação com APIs externas e instituições financeiras. Segue o princípio Open/Closed (S.O.L.I.D.), permitindo acoplar novos provedores sem modificar a lógica central de chamadas da aplicação.

### 4. Encapsulamento Multibanco e Faturamento Digital
O sistema isola a complexidade de regras bancárias e emissão de documentos fiscais:
* Integração Bancária (Sicoob): Implementação modular no diretório `app/Bancos/Sicoob/` contendo geradores de arquivos CNAB240 (Segmentos P, Q, Header e Trailler) via `GeradorRemessa.php`. Na fase atual, o ERP realiza a geração do arquivo de remessa local para download, com escopo preparado para automação via API/FTP.
* Emissão de Notas Fiscais: A arquitetura mapeia credenciais de integração com suporte a certificados digitais (`CertificadoDigitalService` e `IntegracaoCredencial`), projetada para suportar a emissão e faturamento de notas fiscais eletrônicas.

## Segurança e Ciclo de Autenticação
### 1. Autenticação de Dois Fatores (2FA via TOTP)
Proteção estendida de contas integrada à camada de autenticação base:
* Validação via aplicativos autenticadores (Google Authenticator) utilizando bibliotecas auxiliares de TOTP.
* Controle de dispositivos através da tabela `trusted_devices`, que evita solicitações redundantes de token em máquinas previamente autenticadas e autorizadas pelo usuário.

### 2. Middlewares Customizados (`app/Http/Middleware/`)
* `EnsureTwoFactorPassed`: Intercepta requisições direcionadas às rotas críticas do ERP, garantindo o bloqueio caso o fluxo de verificação do segundo fator não tenha sido concluído com sucesso.
* `CheckUserType`: Middleware responsável pelo controle de acessos inicial. Valida o tipo de usuário com base em um array estático de permissões configurado diretamente na assinatura da rota, retornando `403 Forbidden` caso o perfil não seja atendido. O escopo está arquitetado para receber uma ACL (Access Control List) dinâmica em etapas futuras.
## Módulos do Sistema (Livewire Components e Views)

A interface se divide em componentes reativos altamente especializados:

### Cadastros de Base
* Entidades (Clientes/Fornecedores): Geridos por `CreateEntidade`, `EditEntidade` e `ListEntidade`. Centraliza os dados fiscais vinculando tabelas complementares de contatos e múltiplos endereços.
* Estrutura Financeira: CRUDs completos para Bancos, Tipos de Conta, Centros de Custo (alocação de despesas) e Categorias Financeiras orientadas para estruturação de relatórios gerenciais.

### Contas a Pagar e Receber
Módulo Core focado no ciclo de vida de `TitulosFinanceiros` e `Parcelas`:
* Fluxo de Caixa: Listagem dinâmica e filtros analíticos no componente `ListTitulo`.
* Modais de Ação Rápida: Componentes Livewire acionados em formato de modal para liquidação e conciliação de parcelas (`PagarParcela`, `ReceberParcela`), inserção de arquivos em anexo (`Anexos`), além de faturamento individual ou em lote (`GerarCobranca`, `GerarCobrancaLote`).

### Relatórios Gerenciais
Geração visual através de modais sob demanda integrados ao painel principal:
* Demonstração do Resultado do Exercício (`DREModal`).
* Análise Financeira, Gráficos de Despesas, Monitoramento de Fluxo de Caixa e Resumos de Vendas.

## Configuração do Ambiente de Desenvolvimento

### Pré-requisitos
* PHP >= 8.1
* Composer
* Node.js & NPM
* SGBD Relacional (MySQL / PostgreSQL)

### Instalação

1. Clone o repositório para o ambiente local.
2. Instale as dependências do back-end:
```bash
    composer install
    npm install
    npm run build
    cp .env.example .env e configure o .env
    php artisan key:generate
    php artisan migrate --seed
    php artisan serve
```

