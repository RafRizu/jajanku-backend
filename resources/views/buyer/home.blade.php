@extends('layouts.app')
@section('title', 'Beranda - Warung Bu Ipa')

@section('content')
{{-- ── Hero ──────────────────────────────────────────────────────── --}}
<div style="background:linear-gradient(135deg,#FF6B35 0%,#FF8C42 60%,#FFAB5B 100%); padding:24px 16px 56px; position:relative; overflow:hidden;">
    {{-- decorative blob --}}
    <div style="position:absolute;top:-40px;right:-40px;width:160px;height:160px;
                background:rgba(255,255,255,.08);border-radius:50%;"></div>
    <div style="position:absolute;bottom:-20px;left:-20px;width:100px;height:100px;
                background:rgba(255,255,255,.06);border-radius:50%;"></div>

    <p class="text-white mb-1" style="opacity:.85;font-size:.85rem;">👋 Halo, {{ auth()->user()->name }}!</p>
    <h1 class="text-white fw-800 mb-0" style="font-size:1.35rem; line-height:1.3;">
        Temukan jajanan<br>SD mu disini! 🍢
    </h1>
</div>

{{-- ── Warung Info Card (overlaps hero) ──────────────────────────── --}}
<div style="margin-top:-32px; padding:0 14px;">
    @if($shop)
    <div class="card border-0 mb-3" style="border-radius:20px; box-shadow:0 8px 24px rgba(0,0,0,.12); overflow:hidden;">
        {{-- shop image / banner --}}
        @if($shop->image)
            <img src="{{ Storage::url($shop->image) }}" alt="{{ $shop->name }}"
                 style="width:100%;height:130px;object-fit:cover;">
        @else
            <div style="width:100%;height:130px;
                        background:linear-gradient(135deg,#FFE0CC,#FFD4B5);
                        display:flex;align-items:center;justify-content:center;font-size:4rem;">
                🏪
            </div>
        @endif

        <div class="p-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h2 class="fw-800 mb-1" style="font-size:1rem;color:#1F2937;">{{ $shop->name }}</h2>
                    <p class="text-muted small mb-1 lh-sm">{{ $shop->description }}</p>
                    <p class="small mb-0" style="color:#6B7280;">
                        <i class="bi bi-geo-alt-fill me-1" style="color:#FF6B35;"></i>{{ $shop->address }}
                    </p>
                </div>
                <span class="badge badge-status {{ $shop->status === 'active' ? 'badge-success' : 'badge-danger' }}">
                    {{ $shop->status === 'active' ? '🟢 Buka' : '🔴 Tutup' }}
                </span>
            </div>
            <div class="mt-2 d-flex gap-3 small text-muted">
                <span><i class="bi bi-bag-fill me-1" style="color:#FF6B35;"></i>{{ $shop->activeProducts->count() }} menu</span>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Search Bar Menu ─────────────────────────────────────────── --}}
    <form method="GET" action="{{ route('buyer.home') }}" class="mb-3" id="menu-search-form">
        @if(request('cat'))
            <input type="hidden" name="cat" value="{{ request('cat') }}">
        @endif
        <div class="position-relative">
            <input type="text" name="search" id="menu-search-input"
                   class="form-control form-control-lg bg-white border-0 shadow-sm ps-5 pe-4 rounded-pill"
                   style="font-size:.9rem;"
                   placeholder="🔍 Cari cireng, Pop Ice, seblak..."
                   value="{{ request('search') }}"
                   autocomplete="off">
            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"
               style="font-size:1.05rem;"></i>
            @if(request('search'))
            <a href="{{ route('buyer.home', request()->except('search')) }}"
               class="position-absolute top-50 end-0 translate-middle-y me-3 text-muted text-decoration-none"
               style="font-size:1.1rem;" title="Hapus pencarian">
                <i class="bi bi-x-circle-fill"></i>
            </a>
            @endif
        </div>
    </form>
</div>

{{-- ── Category Filter ─────────────────────────────────────────── --}}
<div class="px-3 mb-2">
    <div class="d-flex gap-2 overflow-auto pb-1" style="scrollbar-width:none;">
        <a href="{{ route('buyer.home', array_merge(request()->query(), ['cat' => null])) }}"
           class="btn btn-sm flex-shrink-0 {{ !request('cat') ? 'btn-primary-custom' : '' }}"
           style="{{ !request('cat') ? '' : 'border:1.5px solid #E5E7EB;color:#374151;' }} border-radius:20px;font-size:.75rem;">
            Semua
        </a>
        @foreach($categories as $cat)
        <a href="{{ route('buyer.home', array_merge(request()->query(), ['cat' => $cat->slug])) }}"
           class="btn btn-sm flex-shrink-0 {{ request('cat') === $cat->slug ? 'btn-primary-custom' : '' }}"
           style="{{ request('cat') !== $cat->slug ? 'border:1.5px solid #E5E7EB;color:#374151;' : '' }} border-radius:20px;font-size:.75rem;">
            {{ $cat->icon }} {{ $cat->name }}
        </a>
        @endforeach
    </div>
</div>

{{-- ── Products Grid ───────────────────────────────────────────── --}}
<div class="px-3">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h3 class="section-title mb-0">
            @if(request('search'))
                🔍 Hasil Pencarian "{{ request('search') }}"
            @else
                🍽️ Menu
            @endif
        </h3>
        <span class="small text-muted" id="item-count">{{ $products->total() }} item</span>
    </div>

    <div id="product-list-container">
    @forelse($products as $product)
    <div class="card food-card mb-3 p-3 product-item"
         style="border-radius:16px;"
         data-name="{{ strtolower($product->name) }}"
         data-desc="{{ strtolower($product->description ?? '') }}">
        <div class="d-flex gap-3 align-items-center">
            {{-- product image --}}
            @if($product->image)
                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="product-card-img">
            @else
                <div class="product-card-img-placeholder">
                    @if($product->category?->icon) {{ $product->category->icon }} @else 🍽️ @endif
                </div>
            @endif

            {{-- info --}}
            <div class="flex-fill">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-fill pe-2">
                        <h5 class="fw-700 mb-1" style="font-size:.9rem;color:#1F2937;">{{ $product->name }}</h5>
                        @if($product->description)
                            <p class="text-muted mb-1" style="font-size:.73rem;line-height:1.4;">
                                {{ Str::limit($product->description, 55) }}
                            </p>
                        @endif
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <span class="price-tag fw-700" style="font-size:.9rem;">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </span>
                            @if($product->is_available && $product->stock > 0)
                            <span class="badge rounded-pill text-secondary fw-500" style="font-size:.68rem;background:#F3F4F6;border:1px solid #E5E7EB;">
                                📦 Stok: <strong id="stock-count-{{ $product->id }}">{{ $product->stock }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div>
                        @if($product->is_available && $product->stock > 0)
                        <button class="add-to-cart-btn btn p-0"
                                style="background:linear-gradient(135deg,#FF6B35,#FF8C42);
                                       border-radius:12px;width:38px;height:38px;
                                       display:flex;align-items:center;justify-content:center;border:none;
                                       box-shadow:0 4px 12px rgba(255,107,53,.35);flex-shrink:0;"
                                data-product-id="{{ $product->id }}"
                                data-product-name="{{ $product->name }}">
                            <i class="bi bi-plus-lg text-white fw-700" style="font-size:1rem;"></i>
                        </button>
                        @else
                        <span class="badge-status badge-danger" style="font-size:.65rem;">Habis</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-5">
        <div style="font-size:4rem;">🔍</div>
        <h5 class="fw-600 mt-3 text-muted">Menu "{{ request('search') }}" tidak ditemukan</h5>
        <a href="{{ route('buyer.home') }}" class="btn btn-primary-custom px-4 mt-2">Lihat Semua Menu</a>
    </div>
    @endforelse
    </div>

    @if($products->hasPages())
    <div class="d-flex justify-content-center my-3">
        {{ $products->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

{{-- ── Add-to-cart Toast ───────────────────────────────────────── --}}
<div class="position-fixed bottom-0 start-50 translate-middle-x w-100"
     style="max-width:480px; z-index:900; padding:0 12px 75px; pointer-events:none;">
    <div id="cart-toast" class="d-none" style="pointer-events:auto;">
        <div id="toast-body" class="d-flex align-items-center justify-content-between p-3 rounded-4"
             style="background:#1F2937;color:white;box-shadow:0 8px 24px rgba(0,0,0,.35);">
            <div>
                <i id="toast-icon" class="bi bi-bag-check-fill me-2" style="color:#FF6B35;"></i>
                <span id="toast-text" class="small fw-600">Ditambahkan!</span>
            </div>
            <a href="{{ route('buyer.cart') }}" id="toast-link"
               class="btn btn-sm fw-600"
               style="background:#FF6B35;color:white;border-radius:10px;padding:4px 14px;font-size:.75rem;">
                Keranjang
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let cartCount = parseInt('{{ session("cart") ? count(session("cart")) : 0 }}') || 0;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Instant client-side search filtering
    const searchInput = document.getElementById('menu-search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const items = document.querySelectorAll('.product-item');
            let visibleCount = 0;

            items.forEach(item => {
                const name = item.dataset.name || '';
                const desc = item.dataset.desc || '';
                if (name.includes(query) || desc.includes(query)) {
                    item.style.display = '';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            const countEl = document.getElementById('item-count');
            if (countEl) countEl.textContent = `${visibleCount} item`;
        });
    }

    // Add to cart logic with dynamic stock response handling
    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', async function () {
            const productId   = this.dataset.productId;
            const productName = this.dataset.productName;
            const icon        = this.querySelector('i');

            icon.className = 'bi bi-hourglass text-white';
            this.disabled  = true;

            try {
                const res = await fetch('{{ route("buyer.cart.add") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ product_id: productId, quantity: 1 })
                });
                const data = await res.json();

                const toast = document.getElementById('cart-toast');
                const toastBody = document.getElementById('toast-body');
                const toastIcon = document.getElementById('toast-icon');
                const toastText = document.getElementById('toast-text');
                const toastLink = document.getElementById('toast-link');

                if (data.success) {
                    toastBody.style.background = '#1F2937';
                    toastIcon.className = 'bi bi-bag-check-fill me-2';
                    toastIcon.style.color = '#FF6B35';
                    toastText.textContent = `${productName} ditambahkan!`;
                    toastLink.style.display = 'inline-block';

                    toast.classList.remove('d-none');
                    setTimeout(() => toast.classList.add('d-none'), 3000);

                    cartCount = data.count;
                    updateCartBadge(cartCount);
                } else {
                    toastBody.style.background = '#DC2626';
                    toastIcon.className = 'bi bi-exclamation-triangle-fill me-2';
                    toastIcon.style.color = '#FFFFFF';
                    toastText.textContent = data.message || 'Gagal menambahkan ke keranjang';
                    toastLink.style.display = 'none';

                    toast.classList.remove('d-none');
                    setTimeout(() => toast.classList.add('d-none'), 4000);
                }
            } catch (err) {
                console.error(err);
            } finally {
                icon.className = 'bi bi-plus-lg text-white fw-700';
                this.disabled  = false;
            }
        });
    });

    function updateCartBadge(count) {
        let badge = document.querySelector('.badge-count');
        if (badge) {
            badge.textContent = count;
        } else if (count > 0) {
            const cartLink = document.querySelector('.cart-badge');
            if (cartLink) {
                const span = document.createElement('span');
                span.className = 'badge-count';
                span.textContent = count;
                cartLink.querySelector('i').after(span);
            }
        }
    }
</script>
@endpush
