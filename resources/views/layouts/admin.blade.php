<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - Rakobuan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @vite(['resources/js/app.js'])
</head>
<body>

    <div id="newOrderNotification" class="notification-bar"></div>
    <div class="admin-container">
        @include('admin.partials.sidebar')

        <main class="main-content">
            <header class="main-header">
                <button id="menu-toggle" class="menu-toggle"><i class="fa-solid fa-bars"></i></button>
                <h2>@yield('title')</h2>
            </header>
            <div class="content-body">
                @yield('content')
            </div>
        </main>
    </div>

    <script src="{{ asset('js/admin.js') }}"></script>
    @stack('scripts')
</body>
</html>