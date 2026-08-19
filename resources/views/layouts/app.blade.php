<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'JajanKu') - Jajanan SD Favoritmu</title>
    <meta name="description" content="Temukan jajanan SD mu disini! Gorengan, bundling porsi, dan minuman segar dari Warung Bu Ipa">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary:       #FF6B35;
            --primary-dark:  #E55A25;
            --secondary:     #2D3748;
            --accent:        #FFC107;
            --bg-light:      #F8F9FF;
            --card-radius:   16px;
            --shadow-sm:     0 2px 8px rgba(0,0,0,.08);
            --shadow-md:     0 4px 20px rgba(0,0,0,.12);
        }

        * { font-family: 'Inter', sans-serif; }
        body { background: #EFEFEF; }

        /* ── Mobile App Shell ───────────────────────────────── */
        .app-wrapper {
            min-height: 100vh;
            max-width: 480px;
            margin: 0 auto;
            background: var(--bg-light);
            position: relative;
            box-shadow: 0 0 40px rgba(0,0,0,.15);
        }

        /* ── Top Navigation ─────────────────────────────────── */
        .app-navbar {
            background: linear-gradient(135deg, var(--primary) 0%, #FF8C42 100%);
            padding: 14px 16px;
            position: sticky;
            top: 0;
            z-index: 100;
            color: white;
        }
        .app-navbar .brand { font-weight: 800; font-size: 1.4rem; letter-spacing: -0.5px; }
        .app-navbar .brand span { color: var(--accent); }

        /* ── Bottom Navigation ──────────────────────────────── */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 480px;
            background: white;
            border-top: 1px solid #EAEAEA;
            display: flex;
            z-index: 1050;
            box-shadow: 0 -4px 16px rgba(0,0,0,.08);
        }
        .bottom-nav a {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 10px 4px;
            color: #9CA3AF;
            text-decoration: none;
            font-size: 0.65rem;
            font-weight: 500;
            transition: color .2s;
        }
        .bottom-nav a.active, .bottom-nav a:hover { color: var(--primary); }
        .bottom-nav a i { font-size: 1.3rem; margin-bottom: 2px; }
        .bottom-nav .cart-badge {
            position: relative;
        }
        .bottom-nav .cart-badge .badge-count {
            position: absolute; top: -4px; right: -8px;
            background: var(--primary); color: white;
            border-radius: 50%; width: 16px; height: 16px;
            font-size: 0.6rem; display: flex; align-items: center; justify-content: center;
        }

        /* ── Page Content ───────────────────────────────────── */
        .page-content { padding-bottom: 80px; }

        /* ── Cards ──────────────────────────────────────────── */
        .food-card {
            border: none;
            border-radius: var(--card-radius);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            transition: transform .2s, box-shadow .2s;
        }
        .food-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }

        .shop-card-img {
            width: 100%; height: 140px;
            object-fit: cover;
            background: linear-gradient(135deg, #FFE0CC, #FFD4B5);
        }
        .shop-card-img-placeholder {
            width: 100%; height: 140px;
            background: linear-gradient(135deg, #FFE0CC, #FFD4B5);
            display: flex; align-items: center; justify-content: center;
            font-size: 3rem;
        }

        .product-card-img { width: 80px; height: 80px; object-fit: cover; border-radius: 12px; }
        .product-card-img-placeholder {
            width: 80px; height: 80px; border-radius: 12px;
            background: linear-gradient(135deg, #FFE0CC, #FFD4B5);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.8rem; flex-shrink: 0;
        }

        /* ── Buttons ────────────────────────────────────────── */
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary), #FF8C42);
            border: none; color: white; border-radius: 12px;
            padding: 12px 24px; font-weight: 600;
            box-shadow: 0 4px 12px rgba(255,107,53,.35);
            transition: transform .15s, box-shadow .15s;
        }
        .btn-primary-custom:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(255,107,53,.45);
            color: white;
        }
        .btn-primary-custom:active { transform: scale(.97); }

        /* ── Badges & Tags ──────────────────────────────────── */
        .badge-status {
            padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 600;
        }
        .badge-warning  { background: #FEF3C7; color: #92400E; }
        .badge-success  { background: #D1FAE5; color: #065F46; }
        .badge-info     { background: #DBEAFE; color: #1E40AF; }
        .badge-primary  { background: #EDE9FE; color: #5B21B6; }
        .badge-orange   { background: #FFEDD5; color: #C2410C; }
        .badge-danger   { background: #FEE2E2; color: #991B1B; }
        .badge-secondary { background: #F3F4F6; color: #374151; }

        /* ── Section Headers ────────────────────────────────── */
        .section-title { font-weight: 700; font-size: 1rem; color: var(--secondary); margin-bottom: 12px; }

        /* ── Price ──────────────────────────────────────────── */
        .price-tag { color: var(--primary); font-weight: 700; }

        /* ── Distance ───────────────────────────────────────── */
        .distance-badge { color: #6B7280; font-size: 0.75rem; }

        /* ── Alert ──────────────────────────────────────────── */
        .toast-container { position: fixed; top: 70px; right: 16px; z-index: 9999; }

        /* ── Form ───────────────────────────────────────────── */
        .form-control, .form-select {
            border-radius: 12px;
            border: 1.5px solid #E5E7EB;
            padding: 10px 14px;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(255,107,53,.15);
        }

        /* ── Order Status Timeline ──────────────────────────── */
        .status-step {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 0;
        }
        .status-dot {
            width: 12px; height: 12px; border-radius: 50%;
            background: #D1D5DB; flex-shrink: 0;
        }
        .status-dot.active { background: var(--primary); }
        .status-dot.done   { background: #10B981; }

        /* ── Scrollbar ──────────────────────────────────────── */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #D1D5DB; border-radius: 4px; }

        @media (min-width: 480px) {
            .app-wrapper { min-height: 100vh; }
        }
    </style>

    @stack('styles')
</head>
<body>

<div class="app-wrapper" id="app">

    <!-- Navbar -->
    <nav class="app-navbar d-flex align-items-center justify-content-between">
        <a href="{{ route('dashboard') }}" class="brand text-decoration-none text-white">
            🏪 Jajan<span>Ku</span>
        </a>
        <div class="d-flex align-items-center gap-3">
            @auth
                <span class="text-white opacity-75 small">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-light rounded-pill px-3">Keluar</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-light rounded-pill px-3">Masuk</a>
            @endauth
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible m-3 mb-0 py-2 rounded-3 border-0" style="background:#D1FAE5;color:#065F46;" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" style="font-size:.7rem;"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible m-3 mb-0 py-2 rounded-3 border-0" style="background:#FEE2E2;color:#991B1B;" role="alert">
            <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" style="font-size:.7rem;"></button>
        </div>
    @endif

    <!-- Page Content -->
    <main class="page-content">
        @yield('content')
    </main>

    <!-- Bottom Navigation -->
    @auth
    @php $role = auth()->user()->getRoleNames()->first(); @endphp
    <nav class="bottom-nav">
        @if($role === 'buyer')
            <a href="{{ route('buyer.home') }}" class="{{ request()->routeIs('buyer.home') ? 'active' : '' }}">
                <i class="bi bi-house-fill"></i> Home
            </a>
            <a href="{{ route('buyer.cart') }}" class="cart-badge {{ request()->routeIs('buyer.cart') ? 'active' : '' }}">
                <i class="bi bi-bag-fill"></i>
                @php $cartCount = session('cart') ? count(session('cart')) : 0; @endphp
                @if($cartCount > 0)
                    <span class="badge-count">{{ $cartCount }}</span>
                @endif
                Keranjang
            </a>
            <a href="{{ route('buyer.orders') }}" class="{{ request()->routeIs('buyer.orders*') ? 'active' : '' }}">
                <i class="bi bi-receipt-cutoff"></i> Pesanan
            </a>
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-person-fill"></i> Profil
            </a>
        @elseif($role === 'seller')
            <a href="{{ route('seller.dashboard') }}" class="{{ request()->routeIs('seller.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-fill"></i> Dashboard
            </a>
            <a href="{{ route('seller.orders') }}" class="{{ request()->routeIs('seller.orders*') ? 'active' : '' }}">
                <i class="bi bi-receipt-cutoff"></i> Pesanan
            </a>
            <a href="{{ route('seller.products') }}" class="{{ request()->routeIs('seller.products*') ? 'active' : '' }}">
                <i class="bi bi-bag-heart-fill"></i> Menu
            </a>
            <a href="{{ route('seller.shop.edit') }}" class="{{ request()->routeIs('seller.shop*') ? 'active' : '' }}">
                <i class="bi bi-shop"></i> Warung
            </a>
        @elseif($role === 'driver')
            <a href="{{ route('driver.jobs') }}" class="{{ request()->routeIs('driver.jobs') ? 'active' : '' }}">
                <i class="bi bi-bicycle"></i> Job
            </a>
            <a href="{{ route('driver.history') }}" class="{{ request()->routeIs('driver.history') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i> Riwayat
            </a>
        @endif
    </nav>
    @endauth

    <form id="logout-form" method="POST" action="{{ route('logout') }}" class="d-none">@csrf</form>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@auth
{{-- ─── Pusher JS SDK ─────────────────────────────────────────────────── --}}
<script src="https://js.pusher.com/8.4/pusher.min.js"></script>
<script>
    // ── Pusher global setup ──────────────────────────────────────────────
    Pusher.logToConsole = {{ config('app.debug') ? 'true' : 'false' }};

    window.pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
        cluster:         '{{ config('broadcasting.connections.pusher.options.cluster') }}',
        forceTLS:        true,
        // Endpoint autentikasi private channel: gunakan CSRF token agar tidak ditolak
        authEndpoint:    '{{ url('/broadcasting/auth') }}',
        auth: {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept':       'application/json',
            }
        }
    });

    // ── Helper: tampilkan toast notifikasi ──────────────────────────────
    window.showToast = function(message, type = 'info') {
        const colors = {
            success: '#D1FAE5',  textSuccess: '#065F46',
            info:    '#DBEAFE',  textInfo:    '#1E40AF',
            warning: '#FEF3C7',  textWarning: '#92400E',
            danger:  '#FEE2E2',  textDanger:  '#991B1B',
        };
        const bg   = colors[type]   || colors.info;
        const text = colors['text' + type.charAt(0).toUpperCase() + type.slice(1)] || colors.textInfo;

        const id   = 'toast-' + Date.now();
        const html = `
            <div id="${id}" class="toast align-items-center border-0 show"
                 role="alert" style="background:${bg};color:${text};border-radius:12px;min-width:240px;">
                <div class="d-flex">
                    <div class="toast-body fw-600 small">${message}</div>
                    <button type="button" class="btn-close btn-close-sm me-2 m-auto"
                            data-bs-dismiss="toast" style="font-size:.65rem;"></button>
                </div>
            </div>`;

        let container = document.getElementById('realtime-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'realtime-toast-container';
            container.style.cssText = 'position:fixed;top:70px;right:12px;z-index:9999;display:flex;flex-direction:column;gap:8px;';
            document.body.appendChild(container);
        }
        container.insertAdjacentHTML('beforeend', html);
        // Auto-remove setelah 5 detik
        setTimeout(() => document.getElementById(id)?.remove(), 5000);
    };

    @php $role = auth()->user()->getRoleNames()->first(); @endphp

    // ── Subscribe berdasarkan role ───────────────────────────────────────
    @if($role === 'buyer')
        // Buyer: dengarkan status pesanan miliknya
        window.myChannel = pusher.subscribe('private-user.{{ auth()->id() }}');

        window.myChannel.bind('order.status.updated', function(data) {
            console.log('[Pusher] order.status.updated', data);

            // Update badge & text di halaman manapun yang menampilkan status pesanan ini
            const badge = document.querySelector(`[data-order-id="${data.order_id}"] .order-status-badge`);
            if (badge) {
                badge.textContent = data.status_label;
            }

            // Jika user sedang di halaman detail pesanan yang sama → update UI
            const isDetailPage = document.body.dataset.orderId == data.order_id;
            if (isDetailPage) {
                updateOrderDetailStatus(data);
            }

            showToast(`🔔 Pesanan #${data.order_id}: ${data.status_label}`, 'success');
        });

    @elseif($role === 'seller')
        // Seller: dengarkan pesanan baru & update status
        @php $shopId = auth()->user()->shop?->id; @endphp
        @if($shopId)
        window.shopChannel = pusher.subscribe('private-shop.{{ $shopId }}');

        window.shopChannel.bind('order.new', function(data) {
            console.log('[Pusher] order.new', data);
            showToast(`🛍️ Pesanan baru dari ${data.buyer_name}! (${data.items_count} item)`, 'success');

            // Jika di halaman orders → tambahkan notif dot / reload
            if (document.getElementById('seller-orders-list')) {
                prependNewOrderBadge(data);
            }
        });

        window.shopChannel.bind('order.status.updated', function(data) {
            console.log('[Pusher] order.status.updated (seller)', data);
            const badge = document.querySelector(`[data-order-id="${data.order_id}"] .order-status-badge`);
            if (badge) badge.textContent = data.status_label;
        });
        @endif

    @elseif($role === 'driver')
        // Driver: dengarkan job baru
        window.driverChannel = pusher.subscribe('driver.jobs');

        window.driverChannel.bind('driver.job.new', function(data) {
            console.log('[Pusher] driver.job.new', data);
            showToast(`🛵 Ada job baru! ke ${data.delivery_address}`, 'warning');

            if (document.getElementById('driver-jobs-list')) {
                prependNewJobBadge(data);
            }
        });
    @endif
</script>
@endauth

<script>
    // Auto-dismiss alerts
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(el => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
            bsAlert.close();
        });
    }, 4000);
</script>
@stack('scripts')
</body>
</html>
