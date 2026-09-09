@extends('layouts.app')
@section('title', 'Kategori')
@section('page-title', 'Manajemen Kategori')
@section('page-subtitle', 'Kelola pengelompokan produk')

@section('topbar-actions')
    <button type="button" class="btn btn-primary btn-sm" onclick="openCreateModal()">
        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Kategori
    </button>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <span>Daftar Kategori</span>
            <span class="badge badge-purple" style="margin-left:8px;">{{ $categories->total() }} total</span>
        </div>
        <button type="button" class="btn btn-primary btn-sm" onclick="openCreateModal()">
            + Tambah Kategori
        </button>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width:50px;">#</th>
                    <th>Nama Kategori</th>
                    <th>Slug</th>
                    <th>Deskripsi</th>
                    <th style="text-align:center;">Jumlah Produk</th>
                    <th style="text-align:right; width:150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $index => $category)
                <tr>
                    <td class="text-muted">{{ $categories->firstItem() + $index }}</td>
                    <td>
                        <div style="font-weight:700; color:var(--text-primary);">{{ $category->name }}</div>
                    </td>
                    <td>
                        <code style="font-size:12px; color:var(--primary-light); background:rgba(5,150,105,0.08); padding:2px 6px; border-radius:4px;">{{ $category->slug }}</code>
                    </td>
                    <td style="color:var(--text-secondary); max-width:280px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                        {{ $category->description ?: '—' }}
                    </td>
                    <td style="text-align:center;">
                        <span class="badge badge-purple">{{ $category->products_count }} produk</span>
                    </td>
                    <td style="text-align:right;">
                        <div class="flex gap-2" style="justify-content:flex-end;">
                            <button type="button" class="btn btn-ghost btn-sm" onclick="openEditModal({{ $category->id }}, '{{ addslashes($category->name) }}', '{{ addslashes($category->description ?? '') }}')">
                                Edit
                            </button>
                            <form action="{{ route('categories.destroy', $category) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus kategori ini? Semua produk di dalamnya akan tetap tersimpan.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding:50px; color:var(--text-muted);">
                        <div style="font-size:32px; margin-bottom:8px; opacity:0.6;">🏷️</div>
                        <div style="font-weight:600;">Belum ada kategori</div>
                        <div style="font-size:12px; margin-top:4px;">Klik tombol "+ Tambah Kategori" untuk membuat kategori baru</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($categories->hasPages())
    <div class="pagination-wrapper">
        <div style="font-size:12px; color:var(--text-muted);">
            Menampilkan {{ $categories->firstItem() }} - {{ $categories->lastItem() }} dari {{ $categories->total() }} kategori
        </div>
        <div>{{ $categories->links('pagination::simple-bootstrap-5') }}</div>
    </div>
    @endif
</div>

{{-- MODAL TAMBAH KATEGORI --}}
<div class="modal-overlay" id="create-modal">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">+ Tambah Kategori Baru</div>
            <button type="button" class="modal-close-btn" onclick="closeCreateModal()">&times;</button>
        </div>
        <form action="{{ route('categories.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Kategori *</label>
                <input type="text" name="name" class="form-control" placeholder="Contoh: Makanan, Minuman, Snack" required autofocus>
                @error('name')<div style="color:#fca5a5; font-size:12px; margin-top:4px;">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi (Opsional)</label>
                <textarea name="description" class="form-control" rows="3" placeholder="Keterangan singkat tentang kategori ini..."></textarea>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                <button type="button" onclick="closeCreateModal()" class="btn btn-ghost">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Kategori</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT KATEGORI --}}
<div class="modal-overlay" id="edit-modal">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">Edit Kategori</div>
            <button type="button" class="modal-close-btn" onclick="closeEditModal()">&times;</button>
        </div>
        <form id="edit-form" method="POST">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Nama Kategori *</label>
                <input type="text" name="name" id="edit-name" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" id="edit-desc" class="form-control" rows="3"></textarea>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                <button type="button" onclick="closeEditModal()" class="btn btn-ghost">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openCreateModal() {
    document.getElementById('create-modal').classList.add('active');
}
function closeCreateModal() {
    document.getElementById('create-modal').classList.remove('active');
}

function openEditModal(id, name, desc) {
    document.getElementById('edit-form').action = '{{ route("categories.update", ":id") }}'.replace(':id', id);
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-desc').value = desc;
    document.getElementById('edit-modal').classList.add('active');
}
function closeEditModal() {
    document.getElementById('edit-modal').classList.remove('active');
}

document.getElementById('create-modal').addEventListener('click', function(e) {
    if (e.target === this) closeCreateModal();
});
document.getElementById('edit-modal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});
</script>
@endpush
