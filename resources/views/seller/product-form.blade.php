@extends('layouts.app')
@section('title', isset($product) ? 'Edit Menu' : 'Tambah Menu')

@section('content')
<div class="p-3">
    <div class="d-flex align-items-center gap-2 mb-3">
        <a href="{{ route('seller.products') }}" class="btn p-1" style="color:#374151;">
            <i class="bi bi-arrow-left" style="font-size:1.2rem;"></i>
        </a>
        <h2 class="fw-700 mb-0" style="font-size:1rem; color:#1F2937;">
            {{ isset($product) ? 'Edit Menu' : 'Tambah Menu Baru' }}
        </h2>
    </div>

    <form method="POST"
          action="{{ isset($product) ? route('seller.products.update', $product) : route('seller.products.store') }}"
          enctype="multipart/form-data">
        @csrf
        @if(isset($product)) @method('PUT') @endif

        <!-- Image Upload -->
        <div class="card border-0 mb-3 p-3 text-center" style="border-radius:16px; box-shadow:0 2px 8px rgba(0,0,0,.06);">
            <div id="image-preview-wrap">
                @if(isset($product) && $product->image)
                    <img src="{{ Storage::url($product->image) }}" id="product-preview"
                         class="rounded-4 mb-2 mx-auto" style="width:100px;height:100px;object-fit:cover;">
                @else
                    <div id="image-placeholder" class="mx-auto mb-2 rounded-4 d-flex align-items-center justify-content-center"
                         style="width:100px;height:100px;background:linear-gradient(135deg,#FFE0CC,#FFD4B5);font-size:2.5rem;">
                        🍽️
                    </div>
                @endif
            </div>
            <label class="btn btn-sm rounded-pill px-3" style="border:1.5px solid #FF6B35;color:#FF6B35;" for="product-img">
                <i class="bi bi-camera me-1"></i>Pilih Foto Menu
            </label>
            <input type="file" name="image" id="product-img" class="d-none" accept="image/*">
        </div>

        <div class="card border-0 mb-3 p-3" style="border-radius:16px; box-shadow:0 2px 8px rgba(0,0,0,.06);">
            <div class="mb-3">
                <label class="form-label fw-600 small text-secondary">Nama Menu *</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $product->name ?? '') }}"
                       placeholder="Mis. Nasi Goreng Spesial" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-600 small text-secondary">Deskripsi</label>
                <textarea name="description" class="form-control" rows="2"
                          placeholder="Ceritakan tentang menu ini...">{{ old('description', $product->description ?? '') }}</textarea>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-6">
                    <label class="form-label fw-600 small text-secondary">Harga (Rp) *</label>
                    <input type="number" name="price" class="form-control @error('price') is-invalid @enderror"
                           value="{{ old('price', $product->price ?? '') }}"
                           placeholder="15000" min="0" required>
                    @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-6">
                    <label class="form-label fw-600 small text-secondary">Stok</label>
                    <input type="number" name="stock" class="form-control"
                           value="{{ old('stock', $product->stock ?? 0) }}"
                           placeholder="0" min="0">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-600 small text-secondary">Kategori *</label>
                <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}"
                            {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->icon }} {{ $cat->name }}
                    </option>
                    @endforeach
                </select>
                @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <button type="submit" class="btn btn-primary-custom w-100 py-3 fw-700">
            <i class="bi bi-check2-all me-2"></i>{{ isset($product) ? 'Simpan Perubahan' : 'Tambahkan Menu' }}
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('product-img').addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = e => {
            const wrap = document.getElementById('image-preview-wrap');
            wrap.innerHTML = `<img src="${e.target.result}" id="product-preview"
                class="rounded-4 mb-2 mx-auto" style="width:100px;height:100px;object-fit:cover;">`;
        };
        reader.readAsDataURL(file);
    });
</script>
@endpush
