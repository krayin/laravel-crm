<?php

namespace SuiteZap\LawFirm\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdditionalChecklistSeeder extends Seeder
{
    public function run()
    {
        $templates = [
            // KIT CIVIL (Genérico)
            [
                'name' => 'Kit Cível (Geral)',
                'area' => 'civil',
                'items' => json_encode([
                    'Cópia do Contrato (Objeto da Ação)',
                    'Troca de Mensagens (Prints/E-mails)',
                    'Comprovantes de Pagamento',
                    'Rol de Testemunhas (Nome/RG/Endereço)'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // KIT PENAL
            [
                'name' => 'Kit Penal / Criminal',
                'area' => 'penal',
                'items' => json_encode([
                    'Boletim de Ocorrência (B.O.)',
                    'Cópia do Inquérito Policial (Se houver)',
                    'Comprovante de Residência Atualizado',
                    'Procuração Criminal Específica'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // KIT CONSUMIDOR
            [
                'name' => 'Kit Consumidor',
                'area' => 'consumidor',
                'items' => json_encode([
                    'Nota Fiscal do Produto/Serviço',
                    'Protocolos de Atendimento (SAC)',
                    'Certificado de Garantia',
                    'Fotos do Defeito/Produto',
                    'Comprovante de Negativação (Serasa/SPC)'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // KIT TRIBUTÁRIO
            [
                'name' => 'Kit Tributário',
                'area' => 'tributario',
                'items' => json_encode([
                    'Notificação de Lançamento / Auto de Infração',
                    'Cópia do Processo Administrativo (PAF)',
                    'Certidão de Dívida Ativa (CDA)',
                    'Comprovantes de Pagamento de Tributos',
                    'Balanços Patrimoniais (Se PJ)'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insere apenas se não existirem (evita duplicidade pelo nome)
        foreach ($templates as $template) {
            $exists = DB::table('law_checklist_templates')->where('name', $template['name'])->exists();
            if (!$exists) {
                DB::table('law_checklist_templates')->insert($template);
            }
        }
    }
}
