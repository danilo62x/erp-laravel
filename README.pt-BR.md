# ERP Financeiro / Laravel

[Read in English](./README.md)

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](./LICENSE) ![Free](https://img.shields.io/badge/price-free-brightgreen)

ERP financeiro empresarial em Laravel 11 + Tailwind: dashboard (saldo, a pagar/receber, vencidos, fluxo de caixa), contas a pagar e a receber, lançamentos, notas fiscais, relatórios (DRE) e fluxo de caixa projetado. i18n pt-BR/en/es, PWA, dark mode.

## Destaques

- Contas a pagar e a receber com status e vencimento
- Fluxo de caixa projetado (entradas/saídas)
- Lançamentos, notas fiscais e plano de contas
- Relatórios DRE + dashboard financeiro
- i18n pt-BR/en/es, PWA, dark mode

## Telas em destaque

- Dashboard: Saldo e fluxo de caixa
- A pagar / receber: Status e vencimentos
- Fluxo de caixa: Projeção por período
- DRE: Resultado por categoria

## Stack

- Laravel 11
- PHP 8.2+
- Blade
- Alpine.js
- Tailwind CSS v4
- ApexCharts
- Vite

## Requisitos

- PHP 8.2 ou mais novo, com Composer
- Node.js 20 ou mais novo, com npm

## Como rodar

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
npm run build
php artisan serve
```

Não precisa de banco de dados: os dados de exemplo ficam em classes PHP e o login é simulado por middleware.

Durante o desenvolvimento, use `npm run dev` no lugar de `npm run build` para ter hot reload dos assets.

Conta de demonstração:

- `financeiro@empresa.com` / `demo123`

## Estrutura do projeto

```
app/
├── Http/  Controllers, Middleware
├── Models/
├── Providers/
├── Support/
resources/  css/, js/, views/
routes/  console.php, web.php
```

## Outras tecnologias

Este template também existe em outras 4 tecnologias, com as mesmas telas e a mesma identidade visual. Todas são gratuitas:

- React 19: https://github.com/danilo62x/erp-react
- Vue 3: https://github.com/danilo62x/erp-vue3
- Angular 19: https://github.com/danilo62x/erp-angular
- HTML estático: https://github.com/danilo62x/erp-html

Mais templates gratuitos (painéis admin, sistemas por segmento e apps Flutter): https://github.com/danilo62x?tab=repositories

## Licença

[MIT](./LICENSE), copyright 2026 Danilo Quinelato.
