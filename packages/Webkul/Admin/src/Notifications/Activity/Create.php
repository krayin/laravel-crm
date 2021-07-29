<?php

namespace Webkul\Admin\Notifications\Activity;

use Illuminate\Mail\Mailable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class Create extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The activity instance.
     *
     * @param  \Webkul\Activity\Contracts\Activity  $activity
     */
    public $activity;

    /**
     * @param  Webkul\Activity\Contracts\Activity  $activity
     * @param  Webkul\User\Contracts\User  $user
     * @return void
     */
    public function __construct($activity, $user)
    {
        $this->activity = $activity;

        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to($this->user->email, $this->user->name)
                    ->subject(trans('admin::app.emails.activities.create-subject', ['title' => $this->activity->title]))
                    ->view('admin::emails.activities.create', [
                        'user'     => $this->user,
                        'activity' => $this->activity,
                    ]);
    }
}
