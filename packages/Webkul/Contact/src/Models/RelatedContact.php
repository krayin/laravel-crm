<?php

namespace Webkul\Contact\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Webkul\Activity\Traits\LogsActivity;
use Webkul\Attribute\Traits\CustomAttribute;
use Webkul\Contact\Contracts\RelatedContact as RelatedContactContract;
use Webkul\Contact\Database\Factories\PersonFactory;


class RelatedContact extends Model implements RelatedContactContract
{
    use CustomAttribute, HasFactory, LogsActivity;

    protected $table = 'related_contacts';

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
        return $this->belongsTo(Person::class)->without('relatedContacts');
    }


}
