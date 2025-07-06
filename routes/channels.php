<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Di sini Anda mendaftarkan semua channel siaran dan aturan otorisasi mereka.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// === CHANNEL-CHANNEL PRIVAT UNTUK ADMIN ===
Broadcast::channel('orders', function ($user) {
    return Auth::check();
});

Broadcast::channel('calls',function ($user) {
    return Auth::check();
});

Broadcast::channel('layout-tables', function ($user) {
    return Auth::check();
});


// === CHANNEL PUBLIK UNTUK PELANGGAN ===
Broadcast::channel('customer-logout.{sessionId}', function ($user, $sessionId) {
    return true;
});

Broadcast::channel('order-status.{sessionId}', function ($user, $sessionId) {
    return true;
});