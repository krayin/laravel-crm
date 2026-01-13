<?php

namespace SuiteZap\LawFirm\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DeadlineResource extends JsonResource
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
            'processo_id' => $this->processo_id,
            'titulo' => $this->titulo,
            'descricao' => $this->descricao,
            'data_vencimento' => $this->data_vencimento ? $this->data_vencimento->format('Y-m-d H:i:s') : null,
            'concluido_em' => $this->concluido_em ? $this->concluido_em->format('Y-m-d H:i:s') : null,
            'tipo' => $this->tipo,
            'status' => $this->status,
            'is_atrasado' => $this->is_atrasado,

            // Context
            'processo_titulo' => $this->processo ? $this->processo->titulo : null,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
