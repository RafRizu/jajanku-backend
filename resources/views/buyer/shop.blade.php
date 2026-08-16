@extends('layouts.app')
@section('title', $shop->name)

@section('content')
<!-- Shop Header -->
<div style="position:relative;">
    @if($shop->image)
        <img src="{{ Storage::url($shop->image) }}" alt="{{ $shop->name }}"
             style="width:100%; height:200px; object-fit:cover;">
    @else
        <div style="width:100%;height:200px;background:linear-gradient(135deg,#FFE0CC,#FFD4B5);
                    display:flex;align-items:center;justify-content:center;font-size:5rem;">🏪</div>
    @endif
    <a href="{{ route('buyer.home') }}"
       style="position:absolute;top:12px;left:12px;background:white;border-radius:50%;
              width:38px;height:38px;display:flex;align-items:center;justify-content:center;
              box-shadow:0 2px 8px rgba(0,0,0,.2); text-decoration:none;">
        <i class="bi bi-arrow-left" style="color:#374151;"></i>
    </a>
</div>

<!-- Shop Info Card -->
<div class="mx-3" style="margin-top:-20px; position:relative; z-index:10;">
    <div class="card border-0 p-3 mb-3" style="border-radius:20px; box-shadow:0 4px 20px rgba(0,0,0,.1);">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h1 class="fw-800 mb-1" style="font-size:1.1rem; color:#1F2937;">{{ $shop->name }}</h1>
                <p class="text-muted small mb-1">{{ $shop->description }}</p>
                <p class="small mb-0" style="color:#6B7280;">
                    <i class="bi bi-geo-alt me-1" style="color:#FF6B35;"></i>{{ $shop->address }}
                </p>
            </div>
            <span class="badge {{ $shop->status === 'active' ? 'badge-success' : 'badge-danger' }} badge-status">
                {{ $shop->status === 'active' ? 'Buka' : 'Tutup' }}
            </span>
        </div>
    </div>
</div>

<!-- Category Filter -->
<div class="px-3 mb-3">
    <div class="d-flex gap-2 overflow-auto pb-1" style="scrollbar-width:none;">
        <a href="{{ route('buyer.shop', $shop->id) }}"
           class="btn btn-sm flex-shrink-0 {{ !$activeCategory ? 'btn-primary-custom' : '' }}"
           style="{{ !$activeCategory ? '' : 'border:1.5px solid #E5E7EB;color:#374151;' }} border-radius:20px;font-size:.75rem;">
            Semua
        </a>
        @foreach($categories as $cat)
        <a href="{{ route('buyer.shop', ['id' => $shop->id, 'category' => $cat->id]) }}"
           class="btn btn-sm flex-shrink-0 {{ $activeCategory == $cat->id ? 'btn-primary-custom' : '' }}"
           style="{{ $activeCategory != $cat->id ? 'border:1.5px solid #E5E7EB;color:#374151;' : '' }} border-radius:20px;font-size:.75rem;">
            {{ $cat->icon }} {{ $cat->name }}
        </a>
        @endforeach
    </div>
</div>

<!-- Products List -->
<div class="px-3">
    <h3 class="section-title">Menu</h3>

    @forelse($products as $product)
    <div class="card food-card mb-3 p-3" style="border-radius:16px;">
        <div class="d-flex gap-3 align-items-center">
            <!-- Product Image -->
            @if($product->image)
                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="product-card-img">
            @else
                <div class="product-card-img-placeholder">🍽️</div>
            @endif

            <!-- Product Info -->
            <div class="flex-fill">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="fw-600 mb-1" style="font-size:.9rem; color:#1F2937;">{{ $product->name }}</h5>
                        @if($product->description)
                            <p class="text-muted mb-1" style="font-size:.75rem; line-height:1.4;">
                                {{ Str::limit($product->description, 50) }}
                            </p>
                        @endif
                        <span class="price-tag">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                    </div>
                    <div>
                        @if($product->is_available)
                        <button class="btn add-to-cart-btn p-2"
                                style="background:#FF6B35;border-radius:12px;min-width:36px;height:36px;
                                       display:flex;align-items:center;justify-content:center;border:none;"
                                data-product-id="{{ $product->id }}"
                                data-product-name="{{ $product->name }}">
                            <i class="bi bi-plus-lg text-white fw-700"></i>
                        </button>
                        @else
                        <span class="badge-status badge-danger">Habis</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-5">
        <div style="font-size:3rem;">😔</div>
        <p class="text-muted mt-2">Belum ada menu di kategori ini</p>
    </div>
    @endforelse

    @if($products->hasPages())
        {{ $products->links('pagination::bootstrap-5') }}
    @endif
</div>

<!-- Add to Cart Toast -->
<div class="position-fixed bottom-0 start-50 translate-middle-x w-100" style="max-width:480px; z-index:500; padding:0 12px 90px;">
    <div id="cart-toast" class="d-none">
        <div class="d-flex align-items-center justify-content-between p-3 rounded-4"
             style="background:#1F2937; color:white; box-shadow:0 8px 24px rgba(0,0,0,.3);">
            <div>
                <i class="bi bi-bag-check-fill me-2" style="color:#FF6B35;"></i>
                <span id="toast-text" class="small fw-600">Ditambahkan!</span>
            </div>
            <a href="{{ route('buyer.cart') }}"
               class="btn btn-sm fw-600"
               style="background:#FF6B35;color:white;border-radius:10px;padding:4px 12px;font-size:.75rem;">
                Lihat Keranjang
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let cartCount = parseInt('{{ session("cart") ? count(session("cart")) : 0 }}') || 0;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', async function () {
            const productId   = this.dataset.productId;
            const productName = this.dataset.productName;
            const icon        = this.querySelector('i');

            // Loading state
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

                if (data.success) {
                    // Show toast
                    const toast = document.getElementById('cart-toast');
                    document.getElementById('toast-text').textContent = `${productName} ditambahkan!`;
                    toast.classList.remove('d-none');
                    setTimeout(() => toast.classList.add('d-none'), 3000);

                    // Update badge
                    cartCount = data.count;
                    updateCartBadge(cartCount);
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
        const badge = document.querySelector('.badge-count');
        if (badge) {
            badge.textContent = count;
        } else if (count > 0) {
            const cartLink = document.querySelector('.cart-badge');
            if (cartLink) {
                const span = document.createElement('span');
                span.className = 'badge-count';
                span.textContent = count;
                cartLink.querySelector('i').parentNode.insertBefore(span, cartLink.querySelector('i').nextSibling);
            }
        }
    }
</script>
@endpush
