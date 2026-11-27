<?php

namespace App\Support;

/**
 * Canonical mock data for the business Financial ERP template (accounts
 * payable / receivable, cash flow, P&L / DRE) — mirrors erp/react
 * src/data/{mock,query,types}.ts + lib/format.ts.
 *
 * Raw values (descriptions, amounts, names, numbers) live here; human labels
 * render via the JS i18n bundle (statuses, category groups, cost centers,
 * entry types). The resolved arrays are serialized into the page (window.ER)
 * so the Alpine stores read the same source:
 *   er-fin -> entries / accounts / parties / invoices / categories + filters
 * AP/AR/cash-flow/DRE/metrics logic also lives in JS (window.erHelpers),
 * ported from query.ts.
 */
class ErData
{
    // Deterministic pseudo-random (mirrors mock.ts mulberry32) so the dataset
    // is stable across reloads/builds.
    private static int $rngState = 0;

    private static function seed(int $seed): void
    {
        self::$rngState = $seed & 0xFFFFFFFF;
    }

    private static function rng(): float
    {
        $a = self::$rngState & 0xFFFFFFFF;
        $a = ($a + 0x6D2B79F5) & 0xFFFFFFFF;
        self::$rngState = $a;
        $t = self::imul($a ^ (($a >> 15) & 0x1FFFF), 1 | $a);
        $t = (($t + self::imul($t ^ (($t >> 7) & 0x1FFFFFF), 61 | $t)) ^ $t) & 0xFFFFFFFF;
        $t = ($t ^ (($t >> 14) & 0x3FFFF)) & 0xFFFFFFFF;
        return $t / 4294967296;
    }

    /** 32-bit integer multiply (mirrors Math.imul). */
    private static function imul(int $a, int $b): int
    {
        $a &= 0xFFFFFFFF;
        $b &= 0xFFFFFFFF;
        $ah = ($a >> 16) & 0xFFFF;
        $al = $a & 0xFFFF;
        $bh = ($b >> 16) & 0xFFFF;
        $bl = $b & 0xFFFF;
        return (($al * $bl) + ((((($ah * $bl) + ($al * $bh)) & 0xFFFF) << 16))) & 0xFFFFFFFF;
    }

    /** @param array<int,mixed> $arr */
    private static function pick(array $arr): mixed
    {
        return $arr[(int) floor(self::rng() * count($arr))];
    }

    private static function between(float $min, float $max): float
    {
        return round(($min + self::rng() * ($max - $min)) * 100) / 100;
    }

    // ── Bank accounts ─────────────────────────────────────────────────────

    /** @return list<array<string,mixed>> */
    public static function accounts(): array
    {
        return [
            ['id' => 'bank-itau', 'name' => 'Conta Movimento', 'bank' => 'Itaú', 'number' => 'Ag 0001 / 12345-6', 'type' => 'corrente', 'balance' => 184320.55, 'color' => '#465fff'],
            ['id' => 'bank-bb', 'name' => 'Conta Reserva', 'bank' => 'Banco do Brasil', 'number' => 'Ag 1234 / 98765-4', 'type' => 'poupanca', 'balance' => 96500.0, 'color' => '#12b76a'],
            ['id' => 'bank-caixa', 'name' => 'Caixa Interno', 'bank' => 'Tesouraria', 'number' => 'Fundo fixo', 'type' => 'caixa', 'balance' => 8240.3, 'color' => '#f79009'],
        ];
    }

    // ── Chart of accounts (plano de contas) ───────────────────────────────

    /** @return list<array<string,mixed>> */
    public static function categories(): array
    {
        return [
            // receitas
            ['id' => 'cat-vendas', 'name' => 'Vendas de produtos', 'type' => 'receita', 'group' => 'receita_operacional'],
            ['id' => 'cat-servicos', 'name' => 'Prestação de serviços', 'type' => 'receita', 'group' => 'receita_operacional'],
            ['id' => 'cat-assinaturas', 'name' => 'Assinaturas / SaaS', 'type' => 'receita', 'group' => 'receita_operacional'],
            ['id' => 'cat-juros-receita', 'name' => 'Rendimentos financeiros', 'type' => 'receita', 'group' => 'receita_financeira'],
            // custos
            ['id' => 'cat-mercadorias', 'name' => 'Compra de mercadorias', 'type' => 'despesa', 'group' => 'custo'],
            ['id' => 'cat-insumos', 'name' => 'Insumos e matéria-prima', 'type' => 'despesa', 'group' => 'custo'],
            ['id' => 'cat-frete', 'name' => 'Frete e logística', 'type' => 'despesa', 'group' => 'custo'],
            // despesas operacionais
            ['id' => 'cat-aluguel', 'name' => 'Aluguel e condomínio', 'type' => 'despesa', 'group' => 'despesa_operacional'],
            ['id' => 'cat-energia', 'name' => 'Energia e utilidades', 'type' => 'despesa', 'group' => 'despesa_operacional'],
            ['id' => 'cat-software', 'name' => 'Software e assinaturas', 'type' => 'despesa', 'group' => 'despesa_operacional'],
            ['id' => 'cat-marketing', 'name' => 'Marketing e publicidade', 'type' => 'despesa', 'group' => 'despesa_operacional'],
            ['id' => 'cat-viagens', 'name' => 'Viagens e representação', 'type' => 'despesa', 'group' => 'despesa_operacional'],
            // pessoal
            ['id' => 'cat-salarios', 'name' => 'Salários e encargos', 'type' => 'despesa', 'group' => 'despesa_pessoal'],
            ['id' => 'cat-beneficios', 'name' => 'Benefícios', 'type' => 'despesa', 'group' => 'despesa_pessoal'],
            // financeiras
            ['id' => 'cat-juros-despesa', 'name' => 'Juros e tarifas bancárias', 'type' => 'despesa', 'group' => 'despesa_financeira'],
            // impostos
            ['id' => 'cat-impostos', 'name' => 'Impostos e tributos', 'type' => 'despesa', 'group' => 'imposto'],
        ];
    }

    // ── Parties (clients + suppliers) ─────────────────────────────────────

    private const CLIENT_NAMES = [
        'Construtora Aurora Ltda', 'Mercado Central S.A.', 'Clínica Vida Plena', 'TechNova Sistemas',
        'Restaurante Sabor & Cia', 'Logística Sul Express', 'Editora Horizonte', 'Academia Movimento',
        'Padaria Pão Quente', 'Studio Criativo Pixel', 'Farmácia Bem Estar', 'Indústria MetalForte',
    ];

    private const SUPPLIER_NAMES = [
        'Distribuidora Atlântico', 'Papelaria Office Plus', 'Energia Luz & Cia', 'CloudHost Serviços',
        'Imobiliária Prime', 'Transportes RápidoCargo', 'Gráfica Impressa Bem', 'Suprimentos Industriais MG',
        'Agência Foco Marketing', 'Contabilidade Exata', 'Telecom Conecta', 'Materiais ConstruBase',
    ];

    private const SEGMENTS = ['Varejo', 'Indústria', 'Serviços', 'Tecnologia', 'Saúde', 'Alimentação', 'Logística', 'Educação'];

    /** @return list<array<string,mixed>> */
    public static function parties(): array
    {
        $parties = [];
        foreach (self::CLIENT_NAMES as $i => $name) {
            $first = strtolower(preg_replace('/[^a-z]/i', '', explode(' ', $name)[0]));
            $parties[] = [
                'id' => 'cli-'.($i + 1),
                'name' => $name,
                'kind' => 'cliente',
                'document' => sprintf('%d.%d.%d/0001-%d', 12 + $i, 345 + $i, 678 + $i, 10 + $i),
                'email' => "financeiro@{$first}.com.br",
                'segment' => self::SEGMENTS[$i % count(self::SEGMENTS)],
            ];
        }
        foreach (self::SUPPLIER_NAMES as $i => $name) {
            $first = strtolower(preg_replace('/[^a-z]/i', '', explode(' ', $name)[0]));
            $parties[] = [
                'id' => 'for-'.($i + 1),
                'name' => $name,
                'kind' => 'fornecedor',
                'document' => sprintf('%d.%d.%d/0001-%d', 45 + $i, 678 + $i, 901 + $i, 20 + $i),
                'email' => "contato@{$first}.com.br",
                'segment' => self::SEGMENTS[($i + 3) % count(self::SEGMENTS)],
            ];
        }
        return $parties;
    }

    // ── Entries (lançamentos) ─────────────────────────────────────────────

    private const REVENUE_DESC = [
        'cat-vendas' => ['Venda pedido', 'Faturamento lote', 'Venda balcão'],
        'cat-servicos' => ['Consultoria mensal', 'Projeto sob medida', 'Manutenção contratual'],
        'cat-assinaturas' => ['Assinatura plano Pro', 'Mensalidade SaaS', 'Licença anual'],
        'cat-juros-receita' => ['Rendimento aplicação', 'Juros sobre saldo'],
    ];

    private const EXPENSE_DESC = [
        'cat-mercadorias' => ['Compra de mercadorias', 'Reposição de estoque'],
        'cat-insumos' => ['Compra de insumos', 'Matéria-prima lote'],
        'cat-frete' => ['Frete entrega', 'Transporte de carga'],
        'cat-aluguel' => ['Aluguel do mês', 'Condomínio comercial'],
        'cat-energia' => ['Conta de energia', 'Água e saneamento'],
        'cat-software' => ['Licença software', 'Assinatura cloud'],
        'cat-marketing' => ['Campanha de mídia', 'Anúncios digitais'],
        'cat-viagens' => ['Viagem comercial', 'Diárias e hospedagem'],
        'cat-salarios' => ['Folha de pagamento', 'Encargos trabalhistas'],
        'cat-beneficios' => ['Vale-refeição', 'Plano de saúde'],
        'cat-juros-despesa' => ['Tarifa bancária', 'Juros de empréstimo'],
        'cat-impostos' => ['DAS / Simples', 'ICMS apurado', 'ISS mensal'],
    ];

    /** ISO datetime at UTC midnight for the given y/m/d (day capped at 28). */
    private static function iso(int $year, int $month, int $day): string
    {
        $d = new \DateTime('@'.gmmktime(0, 0, 0, $month + 1, min($day, 28), $year));
        $d->setTimezone(new \DateTimeZone('UTC'));
        return $d->format('Y-m-d\TH:i:s.000\Z');
    }

    private static function statusFor(string $dueIso, bool $paid): string
    {
        if ($paid) {
            return 'pago';
        }
        return strtotime($dueIso) < time() ? 'vencido' : 'pendente';
    }

    /** @return list<array<string,mixed>> */
    public static function entries(): array
    {
        self::seed(20260530);

        $categories = self::categories();
        $accounts = self::accounts();
        $parties = self::parties();
        $clients = array_values(array_filter($parties, fn ($p) => $p['kind'] === 'cliente'));
        $suppliers = array_values(array_filter($parties, fn ($p) => $p['kind'] === 'fornecedor'));
        $revenueCats = array_values(array_filter($categories, fn ($c) => $c['type'] === 'receita'));
        $expenseCats = array_values(array_filter($categories, fn ($c) => $c['type'] === 'despesa'));
        $costCenters = ['comercial', 'operacoes', 'administrativo', 'financeiro', 'marketing', 'ti'];

        $entries = [];
        $now = new \DateTime();
        $nowYear = (int) $now->format('Y');
        $nowMonth = (int) $now->format('n') - 1; // 0-based
        $seq = 0;

        for ($offset = -4; $offset <= 1; $offset++) {
            $base = new \DateTime();
            $base->setDate($nowYear, $nowMonth + 1 + $offset, 1);
            $year = (int) $base->format('Y');
            $month = (int) $base->format('n') - 1;
            $isFuture = $offset > 0;
            $isCurrent = $offset === 0;

            $revCount = 6;
            $expCount = 5;

            for ($i = 0; $i < $revCount; $i++) {
                $cat = self::pick($revenueCats);
                $day = (int) floor(self::rng() * 26) + 1;
                $dueIso = self::iso($year, $month, $day);
                $paid = $isFuture ? false : ($isCurrent ? self::rng() > 0.5 : self::rng() > 0.18);
                $status = self::statusFor($dueIso, $paid);
                $party = self::pick($clients);
                $seq++;
                $descPool = self::REVENUE_DESC[$cat['id']] ?? ['Receita'];
                $entries[] = [
                    'id' => 'ent-'.$seq,
                    'description' => self::pick($descPool).' #'.(1000 + $seq),
                    'type' => 'receita',
                    'categoryId' => $cat['id'],
                    'amount' => $cat['id'] === 'cat-juros-receita' ? self::between(120, 1800) : self::between(3500, 42000),
                    'dueDate' => $dueIso,
                    'paidDate' => $paid ? self::iso($year, $month, min($day + 1, 28)) : null,
                    'status' => $status,
                    'accountId' => self::pick($accounts)['id'],
                    'costCenter' => self::pick(['comercial', 'operacoes', 'financeiro']),
                    'partyId' => $party['id'],
                    'invoiceNumber' => '000.0'.(10 + $seq),
                    'reconciled' => $paid && self::rng() > 0.3,
                ];
            }

            for ($i = 0; $i < $expCount; $i++) {
                $cat = self::pick($expenseCats);
                $day = (int) floor(self::rng() * 26) + 1;
                $dueIso = self::iso($year, $month, $day);
                $paid = $isFuture ? false : ($isCurrent ? self::rng() > 0.45 : self::rng() > 0.22);
                $status = self::statusFor($dueIso, $paid);
                $party = self::pick($suppliers);
                $amount = match ($cat['id']) {
                    'cat-salarios' => self::between(28000, 52000),
                    'cat-aluguel' => self::between(7000, 12000),
                    'cat-impostos' => self::between(4000, 16000),
                    'cat-mercadorias' => self::between(8000, 30000),
                    default => self::between(500, 9000),
                };
                $seq++;
                $descPool = self::EXPENSE_DESC[$cat['id']] ?? ['Despesa'];
                $entries[] = [
                    'id' => 'ent-'.$seq,
                    'description' => self::pick($descPool).' #'.(1000 + $seq),
                    'type' => 'despesa',
                    'categoryId' => $cat['id'],
                    'amount' => $amount,
                    'dueDate' => $dueIso,
                    'paidDate' => $paid ? self::iso($year, $month, min($day + 1, 28)) : null,
                    'status' => $status,
                    'accountId' => self::pick($accounts)['id'],
                    'costCenter' => self::pick($costCenters),
                    'partyId' => $party['id'],
                    'invoiceNumber' => self::rng() > 0.5 ? '000.1'.(10 + $seq) : null,
                    'reconciled' => $paid && self::rng() > 0.35,
                ];
            }
        }

        usort($entries, fn ($a, $b) => strtotime($b['dueDate']) <=> strtotime($a['dueDate']));

        return $entries;
    }

    // ── Invoices (notas fiscais) ──────────────────────────────────────────

    /** @return list<array<string,mixed>> */
    public static function invoices(): array
    {
        // Continue the same RNG stream after building entries (mirrors mock.ts
        // module evaluation order: ENTRIES then INVOICES).
        self::entries();

        $parties = self::parties();
        $clients = array_values(array_filter($parties, fn ($p) => $p['kind'] === 'cliente'));
        $suppliers = array_values(array_filter($parties, fn ($p) => $p['kind'] === 'fornecedor'));
        $statuses = ['autorizada', 'autorizada', 'autorizada', 'pendente', 'cancelada'];

        $now = new \DateTime();
        $nowYear = (int) $now->format('Y');
        $nowMonth = (int) $now->format('n') - 1;

        $invoices = [];
        for ($i = 0; $i < 20; $i++) {
            $out = self::rng() > 0.45;
            $party = $out ? self::pick($clients) : self::pick($suppliers);
            $monthOffset = -((int) floor(self::rng() * 5));
            $d = new \DateTime();
            $d->setDate($nowYear, $nowMonth + 1 + $monthOffset, (int) floor(self::rng() * 26) + 1);
            $amount = self::between(2500, 38000);
            $invoices[] = [
                'id' => 'nf-'.($i + 1),
                'number' => '000.0'.str_pad((string) (12 + $i), 2, '0', STR_PAD_LEFT).'.'.substr((string) (345 + $i * 7), 0, 3),
                'type' => $out ? 'saida' : 'entrada',
                'partyId' => $party['id'],
                'issueDate' => $d->format('Y-m-d\TH:i:s.000\Z'),
                'amount' => $amount,
                'tax' => round($amount * (0.06 + self::rng() * 0.1) * 100) / 100,
                'status' => self::pick($statuses),
            ];
        }

        usort($invoices, fn ($a, $b) => strtotime($b['issueDate']) <=> strtotime($a['issueDate']));

        return $invoices;
    }
}
