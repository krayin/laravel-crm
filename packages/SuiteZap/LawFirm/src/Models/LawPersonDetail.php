<?php

namespace SuiteZap\LawFirm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Contact\Models\PersonProxy;

class LawPersonDetail extends Model
{
    protected $table = 'law_person_details';

    protected $fillable = [
        'person_id',
        'type',
        // PF
        'cpf',
        'rg',
        'rg_orgao',
        'rg_uf',
        'nacionalidade',
        'estado_civil',
        'profissao',
        'data_nascimento',
        'nome_mae',
        'nome_pai',
        // PJ
        'cnpj',
        'razao_social',
        'inscricao_estadual',
        'inscricao_municipal',
        'cnae',
        'representante_legal',
        // Endereço
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'uf',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
    ];

    /**
     * Get the person that owns this detail.
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(PersonProxy::modelClass());
    }
}
