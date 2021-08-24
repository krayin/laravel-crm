<?php

namespace Webkul\Admin\Listeners;

use Illuminate\Support\Facades\Mail;
use Webkul\Admin\Notifications\Activity\Create;
use Webkul\Admin\Notifications\Activity\Update;

class Activity
{
    /**
     * @param  \Webkul\Activity\Models\Activity  $activity
     * @return void
     */
    public function created($activity)
    {
        if (! in_array($activity->type, ['call', 'meeting', 'lunch'])) {
            return;
        }

        try {
            foreach ($activity->participants as $participant) {
                Mail::queue(new Create($activity, $participant));
            }
        } catch (\Exception $e) {
            report($e);
        }
    }

    /**
     * @param  \Webkul\Activity\Models\Activity  $activity
     * @return void
     */
    public function updated($activity)
    {
        if (! in_array($activity->type, ['call', 'meeting', 'lunch'])) {
            return;
        }

        try {
            foreach ($activity->participants as $participant) {
                Mail::queue(new Update($activity, $participant));
            }
        } catch (\Exception $e) {
            report($e);
        }
    }
}