<?php

namespace SuiteZap\LawFirm\Listeners;

use Illuminate\Support\Facades\Log;

class LeadUpdatedListener
{
    /**
     * Handle the event.
     *
     * @param  mixed  $lead
     * @return void
     */
    public function handle($lead)
    {
        try {
            // Verifica se o lead possui estágio
            if (!isset($lead->stage)) {
                return;
            }

            // Critérios para considerar o Lead como "Ganho"
            // Verificamos code (padrão) e name (tradução/customização)
            $isWon = in_array(strtolower($lead->stage->code), ['won', 'ganho']) ||
                in_array(strtolower($lead->stage->name), ['won', 'ganho']);

            if ($isWon) {
                // Cria a mensagem flash com HTML permitido
                session()->flash('info', 'Lead Ganho! <a href="' . route('admin.processos.create', ['lead_id' => $lead->id]) . '" style="text-decoration: underline; font-weight: bold;">Clique aqui para abrir o Processo Jurídico</a>');
            }
        } catch (\Exception $e) {
            Log::error('Erro no LeadUpdatedListener: ' . $e->getMessage());
        }
    }
}
