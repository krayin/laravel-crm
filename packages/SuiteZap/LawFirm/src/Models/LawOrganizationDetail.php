<?php

namespace SuiteZap\LawFirm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Contact\Models\OrganizationProxy;

class LawOrganizationDetail extends Model
{
    protected $table = 'law_organization_details';

    protected $fillable = [
        'organization_id',
        'cnpj',
        'razao_social',
        'inscricao_estadual',
        'inscricao_municipal',
        'cnae',
        'representante_legal',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'uf',
    ];

    /**
     * Get the organization that owns this detail.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(OrganizationProxy::modelClass());
    }
}
