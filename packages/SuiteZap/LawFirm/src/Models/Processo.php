<?php

namespace SuiteZap\LawFirm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Processo extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'processos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'numero_cnj',
        'titulo',
        'descricao',
        'tribunal',
        'vara',
        'comarca',
        'valor_causa',
        'parte_contraria',
        'fase_processual',
        'status',
        'link_acesso',
        'person_id',
        'user_id',
        'lead_id',
    ];

    /**
     * Get the lead associated with the processo.
     *
     * @return BelongsTo
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(\Webkul\Lead\Models\Lead::class);
    }

    /**
     * Get the person (client) associated with the processo.
     *
     * @return BelongsTo
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(\Webkul\Contact\Models\Person::class);
    }
}
