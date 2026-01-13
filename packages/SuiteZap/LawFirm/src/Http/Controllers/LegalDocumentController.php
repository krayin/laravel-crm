<?php

namespace SuiteZap\LawFirm\Http\Controllers;

use Illuminate\Routing\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use SuiteZap\LawFirm\Models\Processo;
use SuiteZap\LawFirm\Models\LawPersonDetail;

class LegalDocumentController extends Controller
{
    public function downloadProcuration($processId)
    {
        Carbon::setLocale('pt_BR');

        // Carrega Processo + Pessoa
        $process = Processo::with(['person'])->findOrFail($processId);
        $person = $process->person;

        // Busca os detalhes da extensão LawFirm
        $detail = $person ? LawPersonDetail::where('person_id', $person->id)->first() : null;

        // --- 1. MONTAGEM DOS DADOS DO OUTORGANTE ---
        $client = [];

        if ($person) {
            $client['name'] = strtoupper($person->name);

            // Documentos (Prioridade: Extension > Person > Custom Attribute)
            $client['cpf'] = $detail->cpf ?? $person->cpf ?? $person->custom_attributes['cpf'] ?? null;
            $client['rg'] = $detail->rg ?? $person->rg ?? null;

            // Nacionalidade, Estado Civil e Profissão (Se houver na extensão)
            $client['nationality'] = $detail->nacionalidade ?? null;
            $client['civil_status'] = $detail->estado_civil ?? null;
            $client['profession'] = $detail->profissao ?? null;

            // --- LÓGICA DE ENDEREÇO (A Correção Principal) ---
            // Verifica se os campos detalhados da extensão LawFirm estão preenchidos
            if ($detail && ($detail->logradouro || $detail->cep)) {
                // Monta endereço a partir dos campos detalhados
                $parts = [];
                if ($detail->logradouro)
                    $parts[] = $detail->logradouro;
                if ($detail->numero)
                    $parts[] = "nº " . $detail->numero;
                if ($detail->complemento)
                    $parts[] = $detail->complemento;
                if ($detail->bairro)
                    $parts[] = $detail->bairro;

                $cityState = [];
                if ($detail->cidade)
                    $cityState[] = $detail->cidade;
                if ($detail->uf)
                    $cityState[] = $detail->uf;

                if (!empty($cityState))
                    $parts[] = implode('/', $cityState);
                if ($detail->cep)
                    $parts[] = "CEP " . $detail->cep;

                $client['address'] = implode(', ', $parts);
            } else {
                // Fallback: Tenta buscar na tabela addresses padrão do Krayin (caso antigo)
                // REMOVIDO: A tabela 'addresses' não existe no schema padrão do Krayin.
                // Se não houver dados na extensão LawFirm, o endereço fica em branco.
                $client['address'] = null;
            }
        } else {
            // Fallback caso não haja pessoa
            $client['name'] = 'OUTORGANTE NÃO DEFINIDO';
            $client['cpf'] = null;
            $client['rg'] = null;
            $client['nationality'] = null;
            $client['civil_status'] = null;
            $client['profession'] = null;
            $client['address'] = null;
        }

        // --- 2. DADOS DO ESCRITÓRIO & CIDADE ---
        $firmName = core()->getConfigData('lawfirm.settings.general.company_name');
        $firmOAB = core()->getConfigData('lawfirm.settings.general.oab_number');
        $firmAddress = core()->getConfigData('lawfirm.settings.general.address');

        // Dados do Advogado Específico do Processo
        $lawyerSpecificName = $process->advogado_responsavel_nome;
        $lawyerSpecificOAB = $process->advogado_responsavel_oab;

        // --- LÓGICA DE CIDADE (Prioridade: Campo Específico > Parsing) ---
        // Primeiro tenta pegar do campo dedicado 'city' nas configurações
        $cityConfig = core()->getConfigData('lawfirm.settings.general.city');

        if (!empty($cityConfig)) {
            $city = trim($cityConfig);
        } else {
            // Fallback: Tenta extrair do endereço
            $city = 'Local'; // Valor padrão neutro

            if ($firmAddress) {
                // TENTATIVA 1: Procura pelo padrão explícito "Cidade/UF" (Ex: São Paulo/SP)
                if (preg_match('/([\w\s]+)\s*\/\s*([A-Z]{2})/', $firmAddress, $matches)) {
                    $city = trim($matches[1]);
                }
                // TENTATIVA 2: Se não tiver barra, usa a lógica de separadores (hífen)
                else {
                    $parts = preg_split('/[-–]/', $firmAddress);

                    foreach ($parts as $index => $part) {
                        $part = trim($part);
                        // Se achar a UF isolada (Ex: " SP ")
                        if (preg_match('/^[A-Z]{2}$/', $part)) {
                            if (isset($parts[$index - 1])) {
                                $candidate = trim($parts[$index - 1]);
                                // Só aceita se NÃO parecer um bairro
                                if (!preg_match('/^(Jd\.|Jardim|Vila|Rua|Av\.|Alameda)/i', $candidate)) {
                                    $city = $candidate;
                                }
                            }
                            break;
                        }
                    }
                }
            }
        }

        // Limpeza final
        $city = trim($city, " .,;-");

        $dateExtenso = Carbon::now()->translatedFormat('d \d\e F \d\e Y');

        $pdf = Pdf::loadView('lawfirm::documents.pdf.procuration', compact(
            'process',
            'client',
            'firmName',
            'firmOAB',
            'firmAddress',
            'lawyerSpecificName',
            'lawyerSpecificOAB',
            'city',
            'dateExtenso'
        ));

        return $pdf->download('procuracao.pdf');
    }
    public function downloadContract($processId)
    {
        Carbon::setLocale('pt_BR');

        // Carrega Processo + Pessoa
        $process = Processo::with(['person'])->findOrFail($processId);
        $person = $process->person;

        // Busca os detalhes da extensão LawFirm
        $detail = $person ? LawPersonDetail::where('person_id', $person->id)->first() : null;

        // --- 1. MONTAGEM DOS DADOS DO OUTORGANTE ---
        $client = [];

        if ($person) {
            $client['name'] = strtoupper($person->name);

            // Documentos (Prioridade: Extension > Person > Custom Attribute)
            $client['cpf'] = $detail->cpf ?? $person->cpf ?? $person->custom_attributes['cpf'] ?? null;
            $client['rg'] = $detail->rg ?? $person->rg ?? null;

            // Abstração de Documento (CPF/CNPJ)
            $client['doc_type'] = 'CPF';
            $client['doc'] = $client['cpf'] ?? '________________';

            // Nacionalidade, Estado Civil e Profissão (Se houver na extensão)
            $client['nationality'] = $detail->nacionalidade ?? null;
            $client['civil_status'] = $detail->estado_civil ?? null;
            $client['profession'] = $detail->profissao ?? null;

            // --- LÓGICA DE ENDEREÇO (A Correção Principal) ---
            // Verifica se os campos detalhados da extensão LawFirm estão preenchidos
            if ($detail && ($detail->logradouro || $detail->cep)) {
                // Monta endereço a partir dos campos detalhados
                $parts = [];
                if ($detail->logradouro)
                    $parts[] = $detail->logradouro;
                if ($detail->numero)
                    $parts[] = "nº " . $detail->numero;
                if ($detail->complemento)
                    $parts[] = $detail->complemento;
                if ($detail->bairro)
                    $parts[] = $detail->bairro;

                $cityState = [];
                if ($detail->cidade)
                    $cityState[] = $detail->cidade;
                if ($detail->uf)
                    $cityState[] = $detail->uf;

                if (!empty($cityState))
                    $parts[] = implode('/', $cityState);
                if ($detail->cep)
                    $parts[] = "CEP " . $detail->cep;

                $client['address'] = implode(', ', $parts);
            } else {
                // Fallback: Tenta buscar na tabela addresses padrão do Krayin (caso antigo)
                // Se não houver dados na extensão LawFirm, o endereço fica em branco.
                $client['address'] = null;
            }
        } else {
            // Fallback caso não haja pessoa
            $client['name'] = 'OUTORGANTE NÃO DEFINIDO';
            $client['cpf'] = null;
            $client['rg'] = null;
            $client['nationality'] = null;
            $client['civil_status'] = null;
            $client['profession'] = null;
            $client['address'] = null;
        }

        // --- 2. DADOS DO ESCRITÓRIO & CIDADE ---
        $firmName = core()->getConfigData('lawfirm.settings.general.company_name');
        $firmOAB = core()->getConfigData('lawfirm.settings.general.oab_number');
        $firmAddress = core()->getConfigData('lawfirm.settings.general.address');

        // Dados do Advogado Específico do Processo
        $lawyerSpecificName = $process->advogado_responsavel_nome;
        $lawyerSpecificOAB = $process->advogado_responsavel_oab;

        // --- LÓGICA DE CIDADE (Prioridade: Campo Específico > Parsing) ---
        // Primeiro tenta pegar do campo dedicado 'city' nas configurações
        $cityConfig = core()->getConfigData('lawfirm.settings.general.city');

        if (!empty($cityConfig)) {
            $city = trim($cityConfig);
        } else {
            // Fallback: Tenta extrair do endereço
            $city = 'Local'; // Valor padrão neutro

            if ($firmAddress) {
                // TENTATIVA 1: Procura pelo padrão explícito "Cidade/UF" (Ex: São Paulo/SP)
                if (preg_match('/([\w\s]+)\s*\/\s*([A-Z]{2})/', $firmAddress, $matches)) {
                    $city = trim($matches[1]);
                }
                // TENTATIVA 2: Se não tiver barra, usa a lógica de separadores (hífen)
                else {
                    $parts = preg_split('/[-–]/', $firmAddress);

                    foreach ($parts as $index => $part) {
                        $part = trim($part);
                        // Se achar a UF isolada (Ex: " SP ")
                        if (preg_match('/^[A-Z]{2}$/', $part)) {
                            if (isset($parts[$index - 1])) {
                                $candidate = trim($parts[$index - 1]);
                                // Só aceita se NÃO parecer um bairro
                                if (!preg_match('/^(Jd\.|Jardim|Vila|Rua|Av\.|Alameda)/i', $candidate)) {
                                    $city = $candidate;
                                }
                            }
                            break;
                        }
                    }
                }
            }
        }

        // Limpeza final
        $city = trim($city, " .,;-");

        $dateExtenso = Carbon::now()->translatedFormat('d \d\e F \d\e Y');

        $pdf = Pdf::loadView('lawfirm::documents.pdf.contract', compact(
            'process',
            'client',
            'firmName',
            'firmOAB',
            'firmAddress',
            'lawyerSpecificName',
            'lawyerSpecificOAB',
            'city',
            'dateExtenso'
        ));

        return $pdf->download('contrato_honorarios.pdf');
    }
}
