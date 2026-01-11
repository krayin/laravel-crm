<?php

namespace SuiteZap\LawFirm\Models;

use Illuminate\Database\Eloquent\Model;
use SuiteZap\LawFirm\Models\Processo;

class Financial extends Model
{
    protected $table = 'law_financials';

    protected $fillable = [
        'processo_id',
        'tipo',
        'nome',
        'valor',
        'data_vencimento',
        'status',
        'descricao',
        // Novos campos para métricas avançadas
        'issued_at',
        'payment_date',
        'category',
        'payment_method',
    ];

    protected $casts = [
        'data_vencimento' => 'date',
        'issued_at' => 'date',
        'payment_date' => 'date',
        'valor' => 'decimal:2',
    ];

    public function processo()
    {
        return $this->belongsTo(Processo::class);
    }
}
