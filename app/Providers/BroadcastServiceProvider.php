<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{

    public function boot()
    {
        // Paksa semua rute otentikasi broadcast untuk menggunakan middleware 'auth:admin'
        Broadcast::routes(['middleware' => ['web', 'auth:admin']]);

        require base_path('routes/channels.php');
    }
}
