<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id && Auth::guard('admin')->check();
});

// Secara eksplisit periksa menggunakan guard 'admin'
Broadcast::channel('orders', function ($user) {
    return Auth::guard('admin')->check();
});

Broadcast::channel('calls', function ($user) {
    return Auth::guard('admin')->check();
});

Broadcast::channel('layout-tables', function ($user) {
    return Auth::guard('admin')->check();
});

// Channel publik tidak berubah
Broadcast::channel('customer-logout.{sessionId}', fn() => true);
Broadcast::channel('order-status.{sessionId}', fn() => true);