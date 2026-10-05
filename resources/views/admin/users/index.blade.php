@extends('layouts.app')
@section('title', 'Kelola User & Toko')
@section('page-title', 'Kelola User & Toko')
@section('page-subtitle', 'Setiap user memiliki satu toko dengan data yang terpisah')

@section('topbar-actions')
    <button type="button" class="btn btn-primary btn-sm" onclick="openCreateModal()">+ Tambah User</button>
@endsection

@section('content')
@if($errors->any())
    <div class="card" style="margin-bottom:16px; border-color:#dc2626;">
        <div style="padding:14px 18px; color:#fca5a5; font-size:13px;">
            @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <div class="card-title">
            <span>Daftar User</span>
            <span class="badge badge-purple" style="margin-left:8px;">{{ $users->total() }} total</span>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width:50px;">#</th>
                    <th>User</th>
                    <th>Toko</th>
                    <th style="text-align:center;">Produk</th>
                    <th style="text-align:center;">Transaksi</th>
                    <th>Peran</th>
                    <th style="text-align:right; width:150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $index => $user)
                <tr>
                    <td class="text-muted">{{ $users->firstItem() + $index }}</td>
                    <td>
                        <div style="font-weight:700; color:var(--text-primary);">{{ $user->name }}</div>
                        <div style="font-size:12px; color:var(--text-muted);">{{ $user->email }}</div>
                    </td>
                    <td>{{ $user->store?->name ?? '—' }}</td>
                    <td style="text-align:center;">{{ $user->store ? ($productCounts[$user->store->id] ?? 0) : '—' }}</td>
                    <td style="text-align:center;">{{ $user->store ? ($transactionCounts[$user->store->id] ?? 0) : '—' }}</td>
                    <td>
                        @if($user->is_admin)
                            <span class="badge badge-purple">Admin</span>
                        @else
                            <span class="badge">Pemilik Toko</span>
                        @endif
                    </td>
                    <td style="text-align:right;">
                        <div class="flex gap-2" style="justify-content:flex-end;">
                            <button type="button" class="btn btn-ghost btn-sm"
                                data-url="{{ route('admin.users.update', $user) }}"
                                data-name="{{ $user->name }}"
                                data-email="{{ $user->email }}"
                                data-store="{{ $user->store?->name }}"
                                onclick="openEditModal(this)">Edit</button>
                            @if(! $user->is_admin)
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline;"
                                  onsubmit="return confirm('Hapus user ini? Toko beserta SEMUA produk, pelanggan, dan transaksinya akan ikut terhapus permanen.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div class="pagination-wrapper">
        <div>{{ $users->links('pagination::simple-bootstrap-5') }}</div>
    </div>
    @endif
</div>

{{-- MODAL TAMBAH USER --}}
<div class="modal-overlay" id="create-modal">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">+ Tambah User & Toko</div>
            <button type="button" class="modal-close-btn" onclick="closeModal('create-modal')">&times;</button>
        </div>
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Toko *</label>
                <input type="text" name="store_name" class="form-control" value="{{ old('store_name') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Nama Pemilik *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Email (untuk login) *</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Password * (min. 8 karakter)</label>
                <input type="password" name="password" class="form-control" required autocomplete="new-password">
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                <button type="button" onclick="closeModal('create-modal')" class="btn btn-ghost">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT USER --}}
<div class="modal-overlay" id="edit-modal">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">Edit User</div>
            <button type="button" class="modal-close-btn" onclick="closeModal('edit-modal')">&times;</button>
        </div>
        <form id="edit-form" method="POST">
            @csrf @method('PUT')
            <div class="form-group" id="edit-store-group">
                <label class="form-label">Nama Toko *</label>
                <input type="text" name="store_name" id="edit-store" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Nama Pemilik *</label>
                <input type="text" name="name" id="edit-name" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Email *</label>
                <input type="email" name="email" id="edit-email" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Password Baru (kosongkan jika tidak diubah)</label>
                <input type="password" name="password" class="form-control" autocomplete="new-password">
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                <button type="button" onclick="closeModal('edit-modal')" class="btn btn-ghost">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openCreateModal() { document.getElementById('create-modal').classList.add('active'); }
function closeModal(id) { document.getElementById(id).classList.remove('active'); }
function openEditModal(btn) {
    document.getElementById('edit-form').action = btn.dataset.url;
    document.getElementById('edit-name').value = btn.dataset.name;
    document.getElementById('edit-email').value = btn.dataset.email;
    document.getElementById('edit-store').value = btn.dataset.store || '';
    document.getElementById('edit-store-group').style.display = btn.dataset.store ? '' : 'none';
    document.getElementById('edit-modal').classList.add('active');
}
</script>
@endpush
