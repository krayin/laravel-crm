<?php

namespace SuiteZap\LawFirm\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessDocument extends Model
{
    protected $table = 'law_process_documents';
    protected $fillable = [
        'processo_id',
        'name',
        'status',
        'file_path',
        'notes'
    ];

    public function process()
    {
        return $this->belongsTo(Processo::class, 'processo_id');
    }
}
