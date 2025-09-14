<?php
// routes/channels.php
use Illuminate\Support\Facades\Broadcast;

// Channel ini akan mengizinkan user mendengarkan jika ID-nya cocok
Broadcast::channel('organizer.{organizerId}', function ($user, $organizerId) {
    return (int) $user->id === (int) $organizerId;
});
