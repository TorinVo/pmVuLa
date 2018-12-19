<?php

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

// Broadcast::channel('App.User.{id}', function ($user, $id) {
//     //return (int) $user->id === (int) $id;
//     return true;
// });

// Broadcast::channel('messages.{id}', function ($user, $id) {
//     return true;
// });

Broadcast::channel('messages.{id}', function ($user, $id) {
    return [
        'name'=> $user->name,
        'id' => $user->id
    ];
});

Broadcast::channel('images.{id}', function ($user, $id) {
    return true;
});

Broadcast::channel('chats.{id}', function ($user, $id) {
    return $user->id === (int) $id;
});