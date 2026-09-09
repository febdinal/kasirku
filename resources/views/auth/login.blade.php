<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — {{ \App\Models\Setting::get('store_name', 'VENTRA') }}</title>
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
    @vite(['resources/css/app.css'])
    <style>
        body { display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .auth-container {
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }
        .auth-card {
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            padding: 40px;
            box-shadow: var(--shadow-lg);
        }
        .auth-logo {
            text-align: center;
            margin-bottom: 32px;
        }
        .auth-brand {
            font-size: 36px;
            font-weight: 800;
            background: linear-gradient(135deg, #059669, #10b981);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: 4px;
        }
        .auth-tagline {
            color: var(--text-muted);
            font-size: 13px;
            margin-top: 6px;
        }
        .auth-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 6px;
        }
        .auth-subtitle {
            color: var(--text-muted);
            font-size: 13px;
            margin-bottom: 28px;
        }
        .input-error { border-color: var(--danger) !important; }
        .error-msg { color: #fca5a5; font-size: 12px; margin-top: 4px; }
        .divider { border: none; border-top: 1px solid var(--border); margin: 24px 0; }
    </style>
</head>
<body>
    <div class="auth-container">
        <div style="display:flex; justify-content:flex-end; margin-bottom:12px;">
            <button type="button" class="theme-toggle-btn" id="theme-toggle-btn" title="Ganti Mode Terang / Gelap" aria-label="Ganti Tema">
                <svg class="theme-icon-dark" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <svg class="theme-icon-light" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
            </button>
        </div>
        <div class="auth-card">
            <div class="auth-logo">
                <div class="auth-brand">{{ \App\Models\Setting::get('store_name', 'VENTRA') }}</div>
                <div class="auth-tagline">Point of Sale System</div>
            </div>

            <div class="auth-title">Masuk ke Sistem</div>
            <div class="auth-subtitle">Masukkan kredensial Anda untuk melanjutkan</div>

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        class="form-control {{ $errors->has('email') ? 'input-error' : '' }}"
                        value="{{ old('email') }}"
                        placeholder="admin@ventra.com"
                        autocomplete="email"
                        required
                    >
                    @error('email')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        class="form-control"
                        placeholder="••••••••"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <div style="display:flex; align-items:center; gap:8px; margin-bottom:24px;">
                    <input type="checkbox" id="remember" name="remember" style="accent-color: var(--primary); width:16px; height:16px;">
                    <label for="remember" style="font-size:13px; color:var(--text-secondary); cursor:pointer;">Ingat saya</label>
                </div>

                <button type="submit" id="login-btn" class="btn btn-primary" style="width:100%; justify-content:center; padding:12px;">
                    Masuk
                </button>
            </form>
        </div>
    </div>
    <script>
        const themeToggleBtn = document.getElementById('theme-toggle-btn');
        if (themeToggleBtn) {
            themeToggleBtn.addEventListener('click', () => {
                const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                document.documentElement.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
            });
        }
    </script>
</body>
</html>
