<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/**
 * A user's own notification channel.
 *
 * The audience for a notification is decided when it is written, and each row names the user it
 * belongs to. This check keeps that decision intact on the socket: a user may only subscribe to
 * the channel carrying their own id.
 */
Broadcast::channel('notifications.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
}, ['guards' => ['user']]);
