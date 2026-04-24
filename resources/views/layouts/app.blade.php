<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir Premium</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/css/style.css">
    
    <!-- Midtrans Snap.js -->
    @if(config('midtrans.is_production'))
        <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    @else
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    @endif
</head>
<body>

    <nav class="navbar glass-panel container" style="margin-top: 1rem; margin-bottom: 2rem;">
        <h1 style="margin: 0; font-weight: 800; color: var(--primary-color);">Kasir<span style="color: var(--text-main);">Pro</span></h1>
        <div class="nav-links">
            <a href="{{ route('cashier.index') }}">Menu Penjualan</a>
            <a href="{{ route('menus.index') }}">Pengelolaan Menu</a>
        </div>
    </nav>

    <main class="container">
        @if(session('success'))
            <div class="glass-panel" style="padding: 1rem; margin-bottom: 1rem; background: rgba(16, 185, 129, 0.2); border-color: var(--success-color); color: var(--success-color);">
                {{ session('success') }}
            </div>
        @endif
        
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
