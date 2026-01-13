<?php

namespace SuiteZap\LawFirm\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChecklistTemplateSeeder extends Seeder
{
    public function run()
    {
        $templates = [
            [
                'name' => 'Kit Básico (Obrigatório)',
                'area' => 'geral',
                'items' => json_encode(['RG e CPF (Digitalizado)', 'Comprovante de Residência (Atualizado)', 'Procuração Assinada', 'Declaração de Hipossuficiência']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kit Trabalhista',
                'area' => 'trabalhista',
                'items' => json_encode(['CTPS (Foto e Contratos)', 'Termo de Rescisão (TRCT)', 'Extrato do FGTS', 'Holerites (Últimos 3 meses)']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kit Família (Divórcio/Alimentos)',
                'area' => 'familia',
                'items' => json_encode(['Certidão de Casamento', 'Certidão de Nascimento (Filhos)', 'Lista de Bens (Imóveis/Veículos)', 'Comprovantes de Despesas Escolares']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kit Previdenciário (INSS)',
                'area' => 'previdenciario',
                'items' => json_encode(['CNIS (Extrato Previdenciário)', 'CTPS (Todas)', 'PPP e LTCAT', 'Senha do MeuINSS']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('law_checklist_templates')->insert($templates);
    }
}
