<?php

namespace SuiteZap\LawFirm\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProcessResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'numero_cnj' => $this->numero_cnj,
            'titulo' => $this->titulo,
            'descricao' => $this->descricao,
            'status' => $this->status,
            'fase_processual' => $this->fase_processual,
            'link_acesso' => $this->link_acesso,
            'link_audiencia' => $this->link_audiencia,
            'data_distribuicao' => $this->data_distribuicao ? $this->data_distribuicao->format('Y-m-d') : null,
            'data_audiencia' => $this->data_audiencia ? $this->data_audiencia->format('Y-m-d H:i:s') : null,
            'valor_causa' => $this->valor_causa,
            'area_direito' => $this->area_direito,
            'subarea_direito' => $this->subarea_direito,

            // Client Info
            'person' => $this->person ? [
                'id' => $this->person->id,
                'name' => $this->person->name,
                'emails' => $this->person->emails,
                'contact_numbers' => $this->person->contact_numbers,
            ] : null,

            // Relations
            'prazos_count' => $this->prazos->count(),
            'total_receita' => $this->receita_total,
            'total_despesa' => $this->despesas_totais,
            'lucro_liquido' => $this->lucro_liquido,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
