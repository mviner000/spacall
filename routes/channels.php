<?php

use Illuminate\Support\Facades\Broadcast;

// Default Laravel private channel for authenticated users
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// ADD THIS: Public channel for todos - anyone can listen
Broadcast::channel('todos', function () {
    return true;  // Public channel - no authentication required
});