<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Post;

Broadcast::channel('App.Models.User.{id}', static function ($user, $id) {
    return (int) $user->id === (int) $id;
});


