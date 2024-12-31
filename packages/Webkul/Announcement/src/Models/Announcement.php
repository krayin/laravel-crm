<?php

namespace Webkul\Announcement\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Announcement\Contracts\Announcement as AnnouncementContract;

class Announcement extends Model implements AnnouncementContract
{
    protected $fillable = [];
}