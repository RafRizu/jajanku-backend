@extends('layouts.app')
@section('title', 'Manajemen Menu')

@section('content')
{{-- ── Header ──────────────────────────────────────────────────── --}}
<div style="background:linear-gradient(135deg,#FF6B35,#FF8C42); padding:20px 16px 44px; position:relative; overflow:hidden;">
    <div style="position:absolute;top:-30px;right:-30px;width:110px;height:110px;
                background:rgba(255,255,255,.08);border-radius:50%;"></div>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="text-white fw-800 mb-0" style="font-size:1.2rem;">
                <i class="bi bi-bag-heart-fill me-2" style="opacity:.9;"></i>Menu Saya
            </h1>
            <p class="mb-0 mt-1 small" style="color:rgba(255,255,255,.75);">Kelola produk warungmu</p>
        </div>
        <a href="{{ route('seller.products.create') }}" class="btn btn-sm"
           style="background:rgba(255,255,255,.2);color:white;border-radius:20px;
                  border:1px solid rgba(255,255,255,.4);font-size:.8rem;font-weight:600;">
            <i class="bi bi-plus-lg me-1"></i>Tambah
        </a>
    </div>
</div>

<div style="margin-top:-20px;padding:0 14px;">
    @if(!$shop)
    <div class="card border-0 p-4" style="border-radius:20px;box-shadow:0 8px 24px rgba(0,0,0,.1);background:#FFF7F5;">
        <p class="fw-700 mb-2" style="color:#C2410C;">⚠️ Setup warung terlebih dahulu!</p>
        <a href="{{ route('seller.shop.edit') }}" class="btn btn-sm btn-primary-custom">Setup Warung</a>
    </div>
    @else

    @forelse($products as $product)
    <div class="card border-0 mb-2 p-3" style="border-radius:16px;box-shadow:0 2px 8px rgba(0,0,0,.06);">
        <div class="d-flex gap-3 align-items-center">
            {{-- image --}}
            @if($product->image)
                <img src="{{ Storage::url($product->image) }}" class="product-card-img" alt="{{ $product->name }}">
            @else
                <div class="product-card-img-placeholder" style="font-size:1.6rem;">
                    @if($product->category?->icon) {{ $product->category->icon }} @else 🍽️ @endif
                </div>
            @endif

            <div class="flex-fill">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <div>
                        <h2 class="fw-700 mb-0" style="font-size:.88rem;color:#1F2937;">{{ $product->name }}</h2>
                        @if($product->category)
                        <span class="badge-status badge-secondary" style="font-size:.62rem;">
                            {{ $product->category->icon }} {{ $product->category->name }}
                        </span>
                        @endif
                    </div>
                    <span class="badge badge-status {{ $product->is_available ? 'badge-success' : 'badge-danger' }}">
                        {{ $product->is_available ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="price-tag fw-700" style="font-size:.9rem;">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                    <span class="small text-muted">Stok: <strong>{{ $product->stock }}</strong></span>
                </div>

                {{-- action buttons --}}
                <div class="d-flex gap-1">
                    <a href="{{ route('seller.products.edit', $product) }}"
                       class="btn btn-sm flex-fill rounded-3"
                       style="border:1.5px solid #E5E7EB;color:#374151;font-size:.72rem;">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                    <form method="POST" action="{{ route('seller.products.toggle', $product) }}" class="flex-fill d-inline">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-sm w-100 rounded-3"
                                style="border:1.5px solid {{ $product->is_available ? '#FCA5A5' : '#6EE7B7' }};
                                       color:{{ $product->is_available ? '#991B1B' : '#065F46' }};font-size:.72rem;">
                            {{ $product->is_available ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('seller.products.destroy', $product) }}" class="d-inline"
                          onsubmit="return confirm('Hapus produk ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm rounded-3"
                                style="border:1.5px solid #FCA5A5;color:#991B1B;font-size:.72rem;width:36px;">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-5 mt-2">
        <div style="font-size:4rem;">🍽️</div>
        <h2 class="fw-700 mt-3 mb-2" style="font-size:1.1rem;color:#1F2937;">Belum Ada Menu</h2>
        <p class="text-muted small mb-4">Tambahkan menu pertamamu sekarang!</p>
        <a href="{{ route('seller.products.create') }}" class="btn btn-primary-custom px-5">Tambah Menu</a>
    </div>
    @endforelse

    @if($products->hasPages())
        {{ $products->links('pagination::bootstrap-5') }}
    @endif
    @endif
</div>
@endsection
