<?php

namespace SuiteZap\LawFirm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Webkul\Activity\Models\Activity;

class Prazo extends Model
{
    protected $table = 'law_processo_prazos';

    protected $fillable = [
        'processo_id',
        'titulo',
        'descricao',
        'data_vencimento',
        'tipo',
        'status',
        'concluido_em',
        'activity_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'data_vencimento' => 'datetime',
        'concluido_em' => 'datetime',
    ];

    /**
     * Get the processo that owns the prazo.
     */
    public function processo(): BelongsTo
    {
        return $this->belongsTo(Processo::class);
    }

    /**
     * Get the activity linked to this prazo.
     */
    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    /**
     * Accessor to check if the deadline is overdue.
     *
     * @return bool
     */
    public function getIsAtrasadoAttribute(): bool
    {
        if ($this->status !== 'pendente') {
            return false;
        }

        return $this->data_vencimento < Carbon::now();
    }
    /**
     * Get the CSS class for the row based on status and deadline.
     *
     * @return string
     */
    public function getRowClassAttribute(): string
    {
        // Default styling
        $baseClass = "bg-white border-b hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-600";
        $vencidoClass = "bg-red-50 border-red-200 border-b hover:bg-red-100 dark:bg-red-900/20";
        $avisoClass = "bg-orange-50 border-orange-200 border-b hover:bg-orange-100 dark:bg-orange-900/20";
        $concluidoClass = "bg-gray-50 border-b dark:bg-gray-800/50";

        // Check completion first
        $status = strtolower($this->status);
        if ($status === 'concluído' || $status === 'concluido') {
            return $concluidoClass;
        }

        if (!$this->data_vencimento) {
            return $baseClass;
        }

        $vencimento = \Carbon\Carbon::parse($this->data_vencimento)->startOfDay();
        $hoje = \Carbon\Carbon::now()->startOfDay();
        $diffDays = $hoje->diffInDays($vencimento, false);

        if ($diffDays <= 0) {
            return $vencidoClass;
        } elseif ($diffDays <= 5) {
            return $avisoClass;
        }

        return $baseClass;
    }

    /**
     * Get the CSS class for the text based on status and deadline.
     *
     * @return string
     */
    public function getTextClassAttribute(): string
    {
        $baseClass = "text-gray-900 dark:text-gray-300";
        $vencidoClass = "text-red-800 font-bold";
        $avisoClass = "text-orange-800 font-medium";
        $concluidoClass = "text-gray-400 line-through";

        $status = strtolower($this->status);
        if ($status === 'concluído' || $status === 'concluido') {
            return $concluidoClass;
        }

        if (!$this->data_vencimento) {
            return $baseClass;
        }

        $vencimento = \Carbon\Carbon::parse($this->data_vencimento)->startOfDay();
        $hoje = \Carbon\Carbon::now()->startOfDay();
        $diffDays = $hoje->diffInDays($vencimento, false);

        if ($diffDays <= 0) {
            return $vencidoClass;
        } elseif ($diffDays <= 5) {
            return $avisoClass;
        }

        return $baseClass;
    }
}
