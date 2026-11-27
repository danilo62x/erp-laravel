<?php

namespace App\Support;

/**
 * Mock authentication (mirrors erp/react stores/auth.ts).
 *
 * Any non-empty email/password logs in. A suggested demo account
 * (financeiro@empresa.com / demo123) is shown on the login screen.
 */
class MockUsers
{
    public const DEMO_EMAIL = 'financeiro@empresa.com';
    public const DEMO_PASSWORD = 'demo123';

    /**
     * @return array{id:int,name:string,email:string,role:string,company:string}|null
     */
    public function authenticate(string $email, string $password): ?array
    {
        if ($email === '' || $password === '') {
            return null;
        }

        $isDemo = $email === self::DEMO_EMAIL;
        $handle = explode('@', $email)[0] ?: 'Financeiro';
        $raw = trim(preg_replace('/[._-]/', ' ', $handle)) ?: 'Financeiro';
        $name = $isDemo ? 'Equipe Financeira' : ucfirst($raw);

        return [
            'id' => 1,
            'name' => $name,
            'email' => $email,
            'role' => 'Controladoria',
            'company' => 'Acme Indústria Ltda',
        ];
    }

    /** @return array<string,mixed> */
    public function register(string $name, string $email): array
    {
        return [
            'id' => 1,
            'name' => $name,
            'email' => $email,
            'role' => 'Administrador',
            'company' => 'Minha Empresa',
        ];
    }
}
