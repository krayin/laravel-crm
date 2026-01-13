<?php

namespace SuiteZap\LawFirm\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistTemplate extends Model
{
    protected $table = 'law_checklist_templates';
    protected $fillable = ['name', 'area', 'items'];

    // Cast automático do JSON para Array
    protected $casts = [
        'items' => 'array',
    ];
}
