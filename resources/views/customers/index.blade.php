@extends('layouts.app')
@section('title', 'Pelanggan')
@section('page-title', 'Manajemen Pelanggan')
@section('page-subtitle', 'Kelola data pelanggan toko')

@section('topbar-actions')
    <button type="button" class="btn btn-primary btn-sm" onclick="openCreateCustModal()">
        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Pelanggan
    </button>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <span>Daftar Pelanggan</span>
            <span class="badge badge-purple" style="margin-left:8px;">{{ $customers->total() }} total</span>
        </div>
        <div style="display:flex; gap:10px; align-items:center;">
            <form method="GET" style="display:flex; gap:8px;">
                <input type="text" name="search" class="form-control" style="width:240px; height:36px; font-size:13px;" placeholder="Cari nama / no. HP..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-ghost btn-sm">Cari</button>
                @if(request('search'))
                    <a href="{{ route('customers.index') }}" class="btn btn-ghost btn-sm">Reset</a>
                @endif
            </form>
            <button type="button" class="btn btn-primary btn-sm" onclick="openCreateCustModal()">
                + Tambah
            </button>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width:50px;">#</th>
                    <th>Nama Pelanggan</th>
                    <th>Kontak (HP / Email)</th>
                    <th>Alamat</th>
                    <th style="text-align:center;">Total Transaksi</th>
                    <th style="text-align:right; width:150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $index => $customer)
                <tr>
                    <td class="text-muted">{{ $customers->firstItem() + $index }}</td>
                    <td>
                        <div style="font-weight:700; color:var(--text-primary);">{{ $customer->name }}</div>
                        <div style="font-size:11px; color:var(--text-muted);">ID: #{{ $customer->id }}</div>
                    </td>
                    <td>
                        <div style="font-size:13px; font-weight:500;">{{ $customer->phone ?: '—' }}</div>
                        @if($customer->email)
                            <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">{{ $customer->email }}</div>
                        @endif
                    </td>
                    <td style="color:var(--text-secondary); max-width:260px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                        {{ $customer->address ?: '—' }}
                    </td>
                    <td style="text-align:center;">
                        <span class="badge badge-purple">{{ $customer->transactions_count }} transaksi</span>
                    </td>
                    <td style="text-align:right;">
                        <div class="flex gap-2" style="justify-content:flex-end;">
                            <button type="button" class="btn btn-ghost btn-sm" onclick="openCustEdit({{ $customer->id }}, '{{ addslashes($customer->name) }}', '{{ $customer->phone }}', '{{ $customer->email }}', '{{ addslashes($customer->address ?? '') }}')">
                                Edit
                            </button>
                            <form action="{{ route('customers.destroy', $customer) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus pelanggan ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding:50px; color:var(--text-muted);">
                        <div style="font-size:32px; margin-bottom:8px; opacity:0.6;">👥</div>
                        <div style="font-weight:600;">Belum ada data pelanggan</div>
                        <div style="font-size:12px; margin-top:4px;">Klik "+ Tambah Pelanggan" untuk mendaftarkan pelanggan baru</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($customers->hasPages())
    <div class="pagination-wrapper">
        <div style="font-size:12px; color:var(--text-muted);">
            Menampilkan {{ $customers->firstItem() }} - {{ $customers->lastItem() }} dari {{ $customers->total() }} pelanggan
        </div>
        <div>{{ $customers->links('pagination::simple-bootstrap-5') }}</div>
    </div>
    @endif
</div>

{{-- MODAL TAMBAH PELANGGAN --}}
<div class="modal-overlay" id="create-cust-modal">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">+ Tambah Pelanggan Baru</div>
            <button type="button" class="modal-close-btn" onclick="closeCreateCustModal()">&times;</button>
        </div>
        <form action="{{ route('customers.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Lengkap *</label>
                <input type="text" name="name" class="form-control" placeholder="Contoh: Budi Santoso" required autofocus>
                @error('name')<div style="color:#fca5a5; font-size:12px; margin-top:4px;">{{ $message }}</div>@enderror
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">No. Telepon / WhatsApp</label>
                    <input type="text" name="phone" class="form-control" placeholder="08xxxxxxxxxx">
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="nama@email.com">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Alamat</label>
                <textarea name="address" class="form-control" rows="2" placeholder="Alamat lengkap pelanggan..."></textarea>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                <button type="button" onclick="closeCreateCustModal()" class="btn btn-ghost">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Pelanggan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT PELANGGAN --}}
<div class="modal-overlay" id="cust-edit-modal">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">Edit Data Pelanggan</div>
            <button type="button" class="modal-close-btn" onclick="closeCustEditModal()">&times;</button>
        </div>
        <form id="cust-edit-form" method="POST">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Nama Lengkap *</label>
                <input type="text" name="name" id="cedit-name" class="form-control" required>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">No. Telepon</label>
                    <input type="text" name="phone" id="cedit-phone" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" id="cedit-email" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Alamat</label>
                <textarea name="address" id="cedit-address" class="form-control" rows="2"></textarea>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                <button type="button" onclick="closeCustEditModal()" class="btn btn-ghost">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openCreateCustModal() {
    document.getElementById('create-cust-modal').classList.add('active');
}
function closeCreateCustModal() {
    document.getElementById('create-cust-modal').classList.remove('active');
}

function openCustEdit(id, name, phone, email, address) {
    document.getElementById('cust-edit-form').action = '{{ route("customers.update", ":id") }}'.replace(':id', id);
    document.getElementById('cedit-name').value = name;
    document.getElementById('cedit-phone').value = phone || '';
    document.getElementById('cedit-email').value = email || '';
    document.getElementById('cedit-address').value = address || '';
    document.getElementById('cust-edit-modal').classList.add('active');
}
function closeCustEditModal() {
    document.getElementById('cust-edit-modal').classList.remove('active');
}

document.getElementById('create-cust-modal').addEventListener('click', function(e) {
    if (e.target === this) closeCreateCustModal();
});
document.getElementById('cust-edit-modal').addEventListener('click', function(e) {
    if (e.target === this) closeCustEditModal();
});
</script>
@endpush
