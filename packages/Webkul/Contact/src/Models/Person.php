<?php

namespace Webkul\Contact\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Webkul\Activity\Models\ActivityProxy;
use Webkul\Activity\Traits\LogsActivity;
use Webkul\Attribute\Traits\CustomAttribute;
use Webkul\Contact\Contracts\Person as PersonContract;
use Webkul\Contact\Database\Factories\PersonFactory;
use Webkul\Tag\Models\TagProxy;
use Webkul\User\Models\UserProxy;

class Person extends Model implements PersonContract
{
    use CustomAttribute, HasFactory, LogsActivity;

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'persons';

    /**
     * Eager loading.
     *
     * @var string
     */
    protected $with = 'organization';

    /**
     * The attributes that are castable.
     *
     * @var array
     */
    protected $casts = [
        'emails'          => 'array',
        'contact_numbers' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'emails',
        'contact_numbers',
        'job_title',
        'user_id',
        'organization_id',
    ];

    /**
     * Get the user that owns the lead.
     */
    public function user()
    {
        return $this->belongsTo(UserProxy::modelClass());
    }

    /**
     * Get the organization that owns the person.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function organization()
    {
        return $this->belongsTo(OrganizationProxy::modelClass());
    }

    /**
     * Get the activities.
     */
    public function activities()
    {
        return $this->belongsToMany(ActivityProxy::modelClass(), 'person_activities');
    }

    /**
     * The tags that belong to the person.
     */
    public function tags()
    {
        return $this->belongsToMany(TagProxy::modelClass(), 'person_tags');
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return PersonFactory::new();
    }
}
