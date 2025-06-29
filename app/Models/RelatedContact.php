<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Webkul\Contact\Models\Person;

class RelatedContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'person_id',
        'eid_expiry',
        'mobile_numbers',
        'emails',
    ];

    protected $casts = [

        'person_id' => 'integer',
        'mobile_numbers' => 'array',
        'emails' => 'array',
        'eid_expiry' => 'date',
    ];


    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
