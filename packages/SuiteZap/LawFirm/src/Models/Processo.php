<?php

namespace SuiteZap\LawFirm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use SuiteZap\LawFirm\Contracts\Processo as ProcessoContract;

class Processo extends Model implements ProcessoContract
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
        'protocolo_distribuicao',
        'titulo',
        'descricao',
        'tribunal',
        'vara',
        'juiz_atual',
        'comarca',
        'valor_causa',
        'parte_contraria',
        'advogado_parte_contraria',
        'fase_processual',
        'status',
        'link_acesso',
        'person_id',
        'user_id',
        'lead_id',
        'data_distribuicao',
        'data_audiencia',
        'area_direito',
        'probabilidade_exito',
        'tipo_parte',
        'cpf_cnpj',
        'advogado_oab',
        'whatsapp_advogado_contrario',
        'email_advogado_contrario',
        'subarea_direito',
        // New Fields Added (Sprint 1 Refinement)
        'opposing_party_name',
        'opposing_party_type',
        'opposing_party_document',
        'link_audiencia', // hearing_link identified in view
    ];

    /**
     * Mutator: Data Distribuicao
     * Fix 1900 issue by converting empty strings to null
     */
    public function setDataDistribuicaoAttribute($value)
    {
        $this->attributes['data_distribuicao'] = $value ?: null;
    }

    /**
     * Mutator: Data Audiencia
     * Fix 1900 issue by converting empty strings to null
     */
    public function setDataAudienciaAttribute($value)
    {
        $this->attributes['data_audiencia'] = $value ?: null;
    }

    /**
     * Set the valor_causa attribute.
     * Cleans "R$ 1.200,50" to 1200.50
     *
     * @param  string  $value
     * @return void
     */
    public function setValorCausaAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['valor_causa'] = null;
            return;
        }

        // Fix: Check if value is already numeric (float/int)
        // If it is, don't try to strip formatting again.
        if (is_numeric($value)) {
            $this->attributes['valor_causa'] = $value;
            return;
        }

        // Remove R$, spaces, and dots (thousands separator)
        $clean = str_replace(['R$', ' ', '.'], '', $value);

        // Replace comma with dot (decimal separator)
        $clean = str_replace(',', '.', $clean);

        $this->attributes['valor_causa'] = (float) $clean;
    }

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'data_distribuicao' => 'date',
        'data_audiencia' => 'datetime',
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

    /**
     * Get the prazos (deadlines) for the processo.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function prazos(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Prazo::class);
    }

    /**
     * Get the financials (revenues/expenses) for the processo.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function financeiros(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Financial::class);
    }

    /**
     * Get the responsible lawyer (user).
     *
     * @return BelongsTo
     */
    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(\Webkul\User\Models\User::class, 'user_id');
    }

    /**
     * Get Total Revenue (Receita) - Excluding Cancelled.
     * Robust check: trim, lowercase, float cast.
     */
    public function getReceitaTotalAttribute()
    {
        return $this->financeiros
            ->filter(function ($item) {
                // Remove espaços e joga pra minúsculo
                $tipo = strtolower(trim($item->tipo));
                $status = strtolower(trim($item->status));

                // Verifica se contém a palavra 'receita' (mais seguro que igualdade estrita)
                return str_contains($tipo, 'receita')
                    && $status !== 'cancelado';
            })
            ->sum(function ($item) {
                return (float) $item->valor;
            });
    }

    /**
     * Get Total Expenses (Despesas) - Excluding Cancelled.
     * Robust check: trim, lowercase, float cast.
     */
    public function getDespesasTotaisAttribute()
    {
        return $this->financeiros
            ->filter(function ($item) {
                $tipo = strtolower(trim($item->tipo));
                $status = strtolower(trim($item->status));
                return str_contains($tipo, 'despesa')
                    && $status !== 'cancelado';
            })
            ->sum(function ($item) {
                return (float) $item->valor;
            });
    }

    /**
     * Get Net Profit (Lucro Líquido).
     */
    public function getLucroLiquidoAttribute()
    {
        return $this->receita_total - $this->despesas_totais;
    }

    /**
     * Get Profit Margin (Margem de Lucratividade).
     */
    public function getMargemLucratividadeAttribute()
    {
        if ($this->receita_total == 0)
            return 0;
        return ($this->lucro_liquido / $this->receita_total) * 100;
    }

    /**
     * Get Success Index (Índice de Êxito).
     */
    public function getIndiceExitoAttribute()
    {
        if ($this->valor_causa == 0)
            return 0;
        return ($this->receita_total / $this->valor_causa) * 100;
    }
    /**
     * Get the CSS class for audience date alert.
     * Logic:
     * - Default: Gray
     * - Past/Today (Ativo/Suspenso): Red + Pulse
     * - Within 5 days (Ativo/Suspenso): Orange
     * - Future (>5 days) (Ativo/Suspenso): Emerald
     *
     * @return string
     */
    public function getAudienciaAlertClassAttribute(): string
    {
        // Default styling
        $defaultClass = "text-gray-600 dark:text-gray-400";

        if (!$this->data_audiencia) {
            return $defaultClass;
        }

        // Only apply alerts if status is active or suspended
        // Doing case-insensitive check to be safe
        $status = strtolower($this->status);
        if ($status !== 'ativo' && $status !== 'suspenso') {
            return $defaultClass;
        }

        $audiencia = \Carbon\Carbon::parse($this->data_audiencia)->startOfDay();
        $hoje = \Carbon\Carbon::now()->startOfDay();
        $diffDays = $hoje->diffInDays($audiencia, false);

        if ($diffDays <= 0) {
            // Overdue or Today
            return "text-red-800 bg-red-100 px-2 py-0.5 rounded font-bold animate-pulse";
        } elseif ($diffDays <= 5) {
            // Urgency Warning
            return "text-orange-600 font-bold";
        } else {
            // Safe
            return "text-emerald-600 font-medium";
        }
    }
    /**
     * Get the attachments (GED) for the processo.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function anexos(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Anexo::class);
    }
}
