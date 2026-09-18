@extends('layouts.app')
@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')
@section('page-subtitle', 'Kelola informasi akun dan keamanan login Anda')

@section('content')
<div style="max-width:720px; margin: 0 auto;">

    {{-- CARD: INFORMASI PROFIL --}}
    <div class="card mb-6">
        <div class="card-header">
            <div class="card-title">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Informasi Profil
            </div>
        </div>
        <div class="card-body">
            {{-- Avatar --}}
            <div style="display:flex; align-items:center; gap:20px; margin-bottom:24px; padding:20px; background:var(--bg-elevated); border-radius:var(--radius-md); border:1px solid var(--border);">
                <div style="width:64px; height:64px; border-radius:50%; background:var(--primary-gradient); display:flex; align-items:center; justify-content:center; font-size:28px; font-weight:800; color:#fff; flex-shrink:0; box-shadow:0 4px 16px rgba(99,102,241,0.4);">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <div style="font-size:18px; font-weight:700; color:var(--text-primary);">{{ $user->name }}</div>
                    <div style="font-size:13px; color:var(--text-muted); margin-top:2px;">{{ $user->email }}</div>
                    <div style="margin-top:6px;">
                        <span class="badge badge-purple">Administrator</span>
                    </div>
                </div>
            </div>

            <form action="{{ route('user.update') }}" method="POST">
                @csrf @method('PUT')
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="name">Nama Lengkap *</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $user->name) }}"
                            placeholder="Nama lengkap Anda"
                            required
                        >
                        @error('name')
                            <div style="font-size:12px; color:var(--danger); margin-top:4px;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="email">Alamat Email *</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $user->email) }}"
                            placeholder="email@domain.com"
                            required
                        >
                        @error('email')
                            <div style="font-size:12px; color:var(--danger); margin-top:4px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div style="display:flex; justify-content:flex-end; margin-top:4px;">
                    <button type="submit" class="btn btn-primary">
                        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Profil
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- CARD: GANTI PASSWORD --}}
    <div class="card mb-6">
        <div class="card-header">
            <div class="card-title">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Ganti Password
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('user.update-password') }}" method="POST">
                @csrf @method('PUT')

                <div class="form-group">
                    <label class="form-label" for="current_password">Password Saat Ini *</label>
                    <div style="position:relative;">
                        <input
                            type="password"
                            id="current_password"
                            name="current_password"
                            class="form-control @error('current_password') is-invalid @enderror"
                            placeholder="Masukkan password saat ini"
                            autocomplete="current-password"
                        >
                        <button type="button" class="password-toggle-btn" onclick="togglePassword('current_password', this)" tabindex="-1" style="position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:var(--text-muted); padding:4px;">
                            <svg class="eye-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                    @error('current_password')
                        <div style="font-size:12px; color:var(--danger); margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="password">Password Baru *</label>
                        <div style="position:relative;">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Minimal 8 karakter"
                                autocomplete="new-password"
                            >
                            <button type="button" class="password-toggle-btn" onclick="togglePassword('password', this)" tabindex="-1" style="position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:var(--text-muted); padding:4px;">
                                <svg class="eye-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                        @error('password')
                            <div style="font-size:12px; color:var(--danger); margin-top:4px;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">Konfirmasi Password Baru *</label>
                        <div style="position:relative;">
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                class="form-control"
                                placeholder="Ulangi password baru"
                                autocomplete="new-password"
                            >
                            <button type="button" class="password-toggle-btn" onclick="togglePassword('password_confirmation', this)" tabindex="-1" style="position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:var(--text-muted); padding:4px;">
                                <svg class="eye-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Password Strength Hint --}}
                <div style="padding:12px 16px; background:var(--bg-elevated); border-radius:var(--radius-md); border:1px solid var(--border); margin-bottom:16px; font-size:12px; color:var(--text-muted);">
                    <strong style="color:var(--text-secondary);">Tips keamanan password:</strong>
                    Gunakan minimal 8 karakter, kombinasikan huruf besar, huruf kecil, angka, dan simbol.
                </div>

                <div style="display:flex; justify-content:flex-end;">
                    <button type="submit" class="btn btn-primary">
                        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Ubah Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- CARD: INFO AKUN --}}
    <div class="card mb-6" style="border-color:var(--border);">
        <div class="card-header">
            <div class="card-title">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Informasi Akun
            </div>
        </div>
        <div class="card-body">
            <div class="grid-2" style="gap:16px;">
                <div style="padding:16px; background:var(--bg-elevated); border-radius:var(--radius-md); border:1px solid var(--border);">
                    <div style="font-size:11px; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px;">Akun Dibuat</div>
                    <div style="font-size:14px; font-weight:600; color:var(--text-primary);">
                        {{ $user->created_at->format('d M Y') }}
                    </div>
                    <div style="font-size:12px; color:var(--text-muted);">{{ $user->created_at->diffForHumans() }}</div>
                </div>
                <div style="padding:16px; background:var(--bg-elevated); border-radius:var(--radius-md); border:1px solid var(--border);">
                    <div style="font-size:11px; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px;">Terakhir Diperbarui</div>
                    <div style="font-size:14px; font-weight:600; color:var(--text-primary);">
                        {{ $user->updated_at->format('d M Y') }}
                    </div>
                    <div style="font-size:12px; color:var(--text-muted);">{{ $user->updated_at->diffForHumans() }}</div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function togglePassword(fieldId, btn) {
    const input = document.getElementById(fieldId);
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';

    const eyeOpen = `<svg class="eye-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>`;
    const eyeSlash = `<svg class="eye-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>`;

    btn.innerHTML = isHidden ? eyeSlash : eyeOpen;
    btn.style.color = isHidden ? 'var(--primary)' : 'var(--text-muted)';
}
</script>
@endpush
