<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ \App\Models\Setting::get('store_name', 'VENTRA') }}</title>
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
<div class="app-layout">
    {{-- SIDEBAR BACKDROP FOR MOBILE --}}
    <div class="sidebar-backdrop" id="sidebar-backdrop"></div>

    {{-- SIDEBAR --}}
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <div class="sidebar-logo-icon">V</div>
            <div class="sidebar-brand-text">
                <div class="brand-name">{{ \App\Models\Setting::get('store_name', 'VENTRA') }}</div>
                <div class="brand-sub">Point of Sale System</div>
            </div>
            <button type="button" class="sidebar-close-btn" id="sidebar-close-btn" title="Tutup Sidebar" aria-label="Tutup Sidebar">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
            </button>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-label">Utama</div>
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>
            <a href="{{ route('pos.index') }}" class="nav-item {{ request()->routeIs('pos.*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Kasir POS
            </a>

            <div class="nav-section-label">Transaksi</div>
            <a href="{{ route('transactions.index') }}" class="nav-item {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Riwayat Transaksi
            </a>

            <div class="nav-section-label">Manajemen</div>
            <a href="{{ route('products.index') }}" class="nav-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Produk
            </a>
            <a href="{{ route('categories.index') }}" class="nav-item {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                Kategori
            </a>
            <a href="{{ route('customers.index') }}" class="nav-item {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Pelanggan
            </a>

            <div class="nav-section-label">Laporan</div>
            <a href="{{ route('reports.daily') }}" class="nav-item {{ request()->routeIs('reports.daily') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Laporan Harian
            </a>
            <a href="{{ route('reports.monthly') }}" class="nav-item {{ request()->routeIs('reports.monthly') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Laporan Bulanan
            </a>
            <a href="{{ route('reports.profit') }}" class="nav-item {{ request()->routeIs('reports.profit') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Laba / Rugi
            </a>

            <div class="nav-section-label">Sistem</div>
            <a href="{{ route('settings.index') }}" class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Pengaturan
            </a>
            <a href="{{ route('user.edit') }}" class="nav-item {{ request()->routeIs('user.*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Profil Saya
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="{{ route('user.edit') }}" class="user-info" style="text-decoration:none; display:flex; align-items:center; gap:12px; padding:10px 12px; border-radius:var(--radius-md); transition:background 0.2s; cursor:pointer;" onmouseover="this.style.background='var(--bg-elevated)'" onmouseout="this.style.background='transparent'">
                <div class="user-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                <div style="min-width:0; flex:1;">
                    <div class="user-name" style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ auth()->user()->name }}</div>
                    <div class="user-role">Administrator</div>
                </div>
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--text-muted); flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            <form action="{{ route('logout') }}" method="POST" style="margin-top:8px;">
                @csrf
                <button type="submit" class="btn btn-ghost btn-sm" style="width:100%; justify-content:center;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- MAIN --}}
    <main class="main-content">
        <div class="topbar">
            <div class="topbar-left">
                <button class="menu-toggle" id="menu-toggle" type="button" aria-label="Buka Menu">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div>
                    <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
                    <div class="topbar-subtitle">@yield('page-subtitle', date('l, d F Y'))</div>
                </div>
            </div>
            <div class="topbar-actions">
                <button type="button" class="theme-toggle-btn" id="theme-toggle-btn" title="Ganti Mode Terang / Gelap" aria-label="Ganti Tema">
                    <svg class="theme-icon-dark" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <svg class="theme-icon-light" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                </button>
                @yield('topbar-actions')
            </div>
        </div>

        <div class="page-content @yield('content-class') fade-in">
            @if(session('success'))
                <div class="alert alert-success">
                    <div class="alert-content">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" class="alert-close" onclick="this.closest('.alert').remove()" aria-label="Tutup">×</button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">
                    <div class="alert-content">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" class="alert-close" onclick="this.closest('.alert').remove()" aria-label="Tutup">×</button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</div>

<script>
    const appLayout = document.querySelector('.app-layout');
    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');
    const sidebarCloseBtn = document.getElementById('sidebar-close-btn');

    // Restore desktop sidebar preference if saved
    if (window.innerWidth > 768 && localStorage.getItem('sidebar_collapsed') === 'true') {
        appLayout.classList.add('sidebar-collapsed');
    }

    function toggleSidebar() {
        if (window.innerWidth <= 768) {
            sidebar.classList.toggle('open');
            backdrop.classList.toggle('active');
        } else {
            const isCollapsed = appLayout.classList.toggle('sidebar-collapsed');
            localStorage.setItem('sidebar_collapsed', isCollapsed ? 'true' : 'false');
        }
    }

    function closeSidebar() {
        if (window.innerWidth <= 768) {
            sidebar.classList.remove('open');
            backdrop.classList.remove('active');
        } else {
            appLayout.classList.add('sidebar-collapsed');
            localStorage.setItem('sidebar_collapsed', 'true');
        }
    }

    if (menuToggle) {
        menuToggle.addEventListener('click', toggleSidebar);
    }
    if (sidebarCloseBtn) {
        sidebarCloseBtn.addEventListener('click', closeSidebar);
    }
    if (backdrop) {
        backdrop.addEventListener('click', closeSidebar);
    }

    // Theme toggle
    const themeToggleBtn = document.getElementById('theme-toggle-btn');
    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            window.dispatchEvent(new CustomEvent('themechange', { detail: { theme: newTheme } }));
        });
    }
</script>
@stack('scripts')
</body>
</html>
