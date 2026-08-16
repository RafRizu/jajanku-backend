@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('content')
<div class="p-3">
    <h2 class="fw-700 mb-3" style="font-size:1.1rem; color:#1F2937;">
        ⚙️ Admin Dashboard
    </h2>
    <div class="card border-0 p-4 text-center" style="border-radius:16px; box-shadow:0 2px 8px rgba(0,0,0,.06);">
        <div style="font-size:3rem;">🛠️</div>
        <h5 class="fw-700 mt-3">Panel Admin</h5>
        <p class="text-muted small">Akses manajemen pengguna, warung, dan kategori melalui API endpoint yang tersedia.</p>
        <a href="/api/shops" target="_blank" class="btn btn-sm btn-primary-custom px-4 mt-2">
            <i class="bi bi-code-slash me-1"></i>API Docs
        </a>
    </div>
</div>
@endsection
