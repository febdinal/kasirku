@extends('layouts.app')
@section('title', 'Kasir POS')
@section('page-title', 'Kasir POS')
@section('page-subtitle', 'Terminal penjualan langsung')
@section('content-class', 'page-content-flush')

@push('styles')
    <style>
        .pos-layout {
            display: grid;
            grid-template-columns: 1fr 380px;
            height: 100%;
            overflow: hidden;
            background: var(--bg-base);
        }

        /* ===== LEFT: PRODUCTS SECTION ===== */
        .pos-products {
            display: flex;
            flex-direction: column;
            padding: 20px 24px;
            overflow: hidden;
            min-width: 0;
        }

        .pos-toolbar {
            display: flex;
            gap: 12px;
            margin-bottom: 14px;
            align-items: center;
        }

        .search-input-wrapper {
            position: relative;
            flex: 1;
        }

        .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            pointer-events: none;
            width: 16px;
            height: 16px;
        }

        .search-input-wrapper .form-control {
            padding-left: 40px;
            height: 42px;
            border-radius: var(--radius-md);
            background: var(--bg-surface);
            border: 1px solid var(--border);
            font-size: 14px;
        }

        .categories-scroll {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            padding-bottom: 8px;
            margin-bottom: 16px;
            scrollbar-width: none;
        }

        .categories-scroll::-webkit-scrollbar {
            display: none;
        }

        .cat-chip {
            flex-shrink: 0;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid var(--border);
            color: var(--text-secondary);
            background: var(--bg-surface);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            white-space: nowrap;
            user-select: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .cat-chip:hover {
            background: var(--bg-elevated);
            color: var(--text-primary);
            border-color: rgba(255, 255, 255, 0.15);
        }

        .cat-chip.active {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
            box-shadow: 0 2px 10px var(--primary-glow);
        }

        .cat-chip-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            height: 18px;
            padding: 0 6px;
            font-size: 11px;
            font-weight: 700;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.08);
            color: var(--text-muted);
            transition: all 0.2s ease;
        }

        .cat-chip:hover .cat-chip-count {
            background: rgba(255, 255, 255, 0.15);
            color: var(--text-primary);
        }

        .cat-chip.active .cat-chip-count {
            background: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }

        /* GRID PRODUK: align-content: start dan grid-auto-rows: max-content agar card tidak melar panjang */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            grid-auto-rows: max-content;
            align-content: start;
            align-items: start;
            gap: 14px;
            overflow-y: auto;
            flex: 1;
            padding-right: 6px;
            padding-bottom: 24px;
        }

        /* KARTU PRODUK: HANYA FOTO, NAMA, DAN KATEGORI */
        .product-card {
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 10px;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            gap: 8px;
            height: fit-content;
            position: relative;
            overflow: hidden;
            user-select: none;
        }

        .product-card:hover {
            border-color: var(--primary);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.35), 0 0 0 1px var(--primary);
            transform: translateY(-2px);
        }

        .product-card.out-of-stock {
            opacity: 0.5;
            cursor: not-allowed;
            filter: grayscale(0.7);
        }

        .product-card.out-of-stock:hover {
            border-color: var(--border);
            box-shadow: none;
            transform: none;
        }

        /* 1. FOTO PRODUK */
        .product-img-wrapper {
            width: 100%;
            height: 110px;
            border-radius: var(--radius-sm);
            overflow: hidden;
            background: var(--bg-elevated);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .product-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.25s ease;
        }

        .product-card:hover .product-img {
            transform: scale(1.05);
        }

        .product-img-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            background: linear-gradient(135deg, rgba(39, 53, 73, 0.6), rgba(5, 150, 105, 0.1));
        }

        .product-img-placeholder svg {
            width: 32px;
            height: 32px;
            opacity: 0.6;
        }

        .out-of-stock-pill {
            position: absolute;
            bottom: 6px;
            left: 6px;
            background: rgba(239, 68, 68, 0.9);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 4px;
            backdrop-filter: blur(4px);
        }

        /* 2. KATEGORI */
        .product-cat-tag {
            font-size: 10px;
            font-weight: 700;
            background: rgba(99, 102, 241, 0.12);
            color: var(--primary-light);
            padding: 2px 8px;
            border-radius: 4px;
            width: fit-content;
            letter-spacing: 0.3px;
        }

        [data-theme="light"] .product-cat-tag {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
            font-weight: 600;
        }

        /* 3. NAMA PRODUK */
        .product-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
            line-height: 1.35;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 34px;
        }

        /* 4. HARGA PRODUK */
        .product-price-row {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            margin-top: auto;
            padding-top: 2px;
        }

        .product-price {
            font-size: 13px;
            font-weight: 700;
            color: var(--primary-light);
            letter-spacing: -0.2px;
        }

        [data-theme="light"] .product-price {
            color: #0f172a;
            font-weight: 800;
            font-size: 13.5px;
        }

        .product-unit {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* ===== RIGHT: CART SECTION ===== */
        .pos-cart {
            background: var(--bg-surface);
            border-left: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            height: 100%;
            box-shadow: -4px 0 20px rgba(0, 0, 0, 0.15);
        }

        .cart-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(39, 53, 73, 0.3);
        }

        .cart-title-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .cart-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .cart-count {
            background: var(--primary);
            color: #fff;
            min-width: 22px;
            height: 22px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            padding: 0 6px;
            transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .cart-customer-bar {
            padding: 12px 20px;
            border-bottom: 1px solid var(--border-subtle);
            background: var(--bg-elevated);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .cart-customer-bar select {
            flex: 1;
            font-size: 12px;
            padding: 6px 10px;
            height: 34px;
            background: var(--bg-surface);
        }

        .cart-items {
            flex: 1;
            overflow-y: auto;
            padding: 14px 16px;
            scrollbar-width: thin;
        }

        .cart-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: var(--radius-md);
            margin-bottom: 8px;
            background: var(--bg-elevated);
            border: 1px solid var(--border-subtle);
            transition: border-color 0.15s;
        }

        .cart-item:hover {
            border-color: rgba(255, 255, 255, 0.1);
        }

        .cart-item-thumb {
            width: 38px;
            height: 38px;
            border-radius: 6px;
            overflow: hidden;
            background: var(--bg-surface);
            border: 1px solid var(--border);
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cart-item-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .cart-item-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            background: var(--bg-base);
        }

        .cart-item-name {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--text-primary);
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            word-break: break-word;
        }

        .qty-control {
            display: flex;
            align-items: center;
            gap: 4px;
            background: var(--bg-surface);
            padding: 2px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border);
        }

        .qty-btn {
            width: 24px;
            height: 24px;
            border-radius: 4px;
            border: none;
            background: var(--bg-elevated);
            color: var(--text-primary);
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s;
        }

        .qty-btn:hover {
            background: var(--primary);
            color: #fff;
        }

        .qty-val {
            min-width: 22px;
            text-align: center;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .cart-item-sub {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-primary);
            min-width: 65px;
            text-align: right;
        }

        .remove-item {
            color: var(--text-muted);
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s;
        }

        .remove-item:hover {
            color: var(--danger);
            background: rgba(239, 68, 68, 0.12);
        }

        .cart-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: var(--text-muted);
            text-align: center;
            gap: 8px;
            padding: 40px 20px;
        }

        /* ===== CART SUMMARY & FOOTER ===== */
        .cart-calculation {
            padding: 14px 20px 10px;
            border-top: 1px solid var(--border);
            background: rgba(15, 23, 42, 0.4);
        }

        .discount-bar {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
        }

        .discount-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            white-space: nowrap;
        }

        .discount-input-wrapper {
            flex: 1;
            position: relative;
        }

        .discount-prefix {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 600;
        }

        .discount-input-wrapper input {
            padding-left: 32px;
            height: 32px;
            font-size: 12px;
            background: var(--bg-surface);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            margin-bottom: 6px;
        }

        .summary-label {
            color: var(--text-secondary);
        }

        .summary-value {
            font-weight: 600;
            color: var(--text-primary);
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            padding-top: 10px;
            margin-top: 8px;
            border-top: 1px solid var(--border);
        }

        .total-label {
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: var(--text-primary);
        }

        .total-value {
            font-size: 20px;
            font-weight: 900;
            color: var(--success);
        }

        .cart-footer {
            padding: 14px 20px 18px;
            background: rgba(15, 23, 42, 0.4);
        }

        .checkout-btn {
            width: 100%;
            padding: 13px 20px;
            border-radius: var(--radius-md);
            font-size: 14px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: none;
            cursor: pointer;
            background: linear-gradient(135deg, var(--success), #059669);
            color: #fff;
            box-shadow: 0 4px 16px rgba(16, 185, 129, 0.35);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .checkout-btn:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.45);
        }

        .checkout-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            box-shadow: none;
            transform: none;
        }

        /* ===== PAYMENT MODAL ===== */
        .payment-summary-box {
            background: linear-gradient(135deg, rgba(5, 150, 105, 0.12), rgba(16, 185, 129, 0.08));
            border: 1px solid rgba(5, 150, 105, 0.25);
            border-radius: var(--radius-md);
            padding: 16px;
            text-align: center;
            margin-bottom: 20px;
        }

        .payment-summary-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .payment-summary-value {
            font-size: 28px;
            font-weight: 900;
            color: var(--primary-light);
            margin-top: 4px;
        }

        .quick-amounts-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin: 12px 0 16px;
        }

        .quick-amount-btn {
            background: var(--bg-elevated);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 8px 10px;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-primary);
            cursor: pointer;
            transition: all 0.15s;
            text-align: center;
        }

        .quick-amount-btn:hover {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
        }

        .change-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            border-radius: var(--radius-md);
            background: var(--bg-elevated);
            border: 1px solid var(--border);
            margin-bottom: 20px;
        }

        .change-box-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
        }

        .change-box-value {
            font-size: 18px;
            font-weight: 800;
            color: var(--success);
        }

        @media (max-width: 768px) {
            .pos-layout {
                grid-template-columns: 1fr;
                height: auto;
                overflow-y: auto;
            }

            .pos-cart {
                border-left: none;
                border-top: 1px solid var(--border);
                height: 600px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="pos-layout">
        {{-- BAGIAN KIRI: DAFTAR PRODUK --}}
        <div class="pos-products">
            <div class="pos-toolbar">
                <div class="search-input-wrapper">
                    <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" id="product-search" class="form-control"
                        placeholder="Cari nama produk atau SKU..." autocomplete="off">
                </div>
            </div>

            {{-- FILTER KATEGORI --}}
            <div class="categories-scroll">
                <button class="cat-chip active" data-cat="">
                    <span>Semua Produk</span>
                    <span class="cat-chip-count">{{ $allProductsCount }}</span>
                </button>
                @foreach ($categories as $cat)
                    <button class="cat-chip" data-cat="{{ $cat->id }}">
                        <span>{{ $cat->name }}</span>
                        <span class="cat-chip-count">{{ $cat->products_count }}</span>
                    </button>
                @endforeach
            </div>

            {{-- GRID PRODUK --}}
            <div class="products-grid" id="products-grid">
                <div style="grid-column:1/-1; text-align:center; padding:50px; color:var(--text-muted);">
                    Memuat data produk...
                </div>
            </div>
        </div>

        {{-- BAGIAN KANAN: KERANJANG (CART) --}}
        <div class="pos-cart">
            <div class="cart-header">
                <div class="cart-title-wrapper">
                    <div class="cart-title">Keranjang Belanja</div>
                    <div class="cart-count" id="cart-count">0</div>
                </div>
                <button class="btn btn-ghost btn-sm" id="clear-cart" title="Kosongkan Keranjang">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Kosongkan
                </button>
            </div>

            <div class="cart-customer-bar">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    style="color:var(--text-muted);">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <select id="customer-select" class="form-control">
                    <option value="">Pelanggan Umum (Walk-in)</option>
                    @foreach ($customers as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}{{ $c->phone ? ' (' . $c->phone . ')' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- LIST ITEM DI KERANJANG --}}
            <div class="cart-items" id="cart-items">
                <div class="cart-empty" id="cart-empty">
                    <div style="font-size:36px; opacity:0.6;">🛒</div>
                    <div style="font-weight:600;">Keranjang masih kosong</div>
                    <div style="font-size:12px;">Pilih produk di sisi kiri untuk memulai transaksi</div>
                </div>
            </div>

            {{-- REKAP HARGA & DISKON --}}
            <div class="cart-calculation">
                <div class="discount-bar">
                    <span class="discount-label">Diskon:</span>
                    <div class="discount-input-wrapper">
                        <span class="discount-prefix">Rp</span>
                        <input type="number" id="discount-input" class="form-control" placeholder="0" min="0">
                    </div>
                </div>

                <div class="summary-row">
                    <span class="summary-label">Subtotal</span>
                    <span class="summary-value" id="subtotal-display">Rp 0</span>
                </div>
                <div class="summary-row" id="discount-row" style="display:none;">
                    <span class="summary-label">Potongan Diskon</span>
                    <span class="summary-value" id="discount-display" style="color:var(--danger);">- Rp 0</span>
                </div>
                <div class="summary-row" id="ppn-row" style="{{ !$ppnEnabled ? 'opacity:0.45;' : '' }}">
                    <span class="summary-label" id="ppn-label">PPN
                        {{ $ppnPercentage > 0 ? $ppnPercentage . '%' : '(nonaktif)' }}</span>
                    <span class="summary-value" id="tax-display">Rp 0</span>
                </div>
                <div class="total-row">
                    <span class="total-label">TOTAL TAGIHAN</span>
                    <span class="total-value" id="total-display">Rp 0</span>
                </div>
            </div>

            {{-- TOMBOL BAYAR --}}
            <div class="cart-footer">
                <button id="checkout-btn" class="checkout-btn" disabled>
                    <span>Proses Pembayaran</span>
                    <span id="checkout-total-badge">Rp 0</span>
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL PEMBAYARAN --}}
    <div class="modal-overlay" id="payment-modal">
        <div class="modal modal-lg">
            <div class="modal-header">
                <div class="modal-title">Konfirmasi Pembayaran</div>
                <button type="button" class="modal-close-btn" id="cancel-payment-icon">&times;</button>
            </div>

            <div class="payment-summary-box">
                <div class="payment-summary-label">Total yang Harus Dibayar</div>
                <div class="payment-summary-value" id="modal-total">Rp 0</div>
            </div>

            <div class="form-group">
                <label class="form-label">Metode Pembayaran</label>
                <select id="payment-method" class="form-control" style="font-size:14px; font-weight:600;">
                    <option value="cash">💵 Tunai (Cash)</option>
                    <option value="transfer">🏦 Transfer Bank</option>
                    <option value="qris">📱 QRIS</option>
                    <option value="debit">💳 Kartu Debit</option>
                    <option value="kredit">💳 Kartu Kredit</option>
                </select>
            </div>

            <div id="cash-section">
                <div class="form-group">
                    <label class="form-label">Uang Diterima (Rp)</label>
                    <input type="number" id="payment-amount" class="form-control" placeholder="0" min="0"
                        style="font-size:18px; font-weight:700; height:46px;">
                </div>

                <div class="form-label" style="margin-bottom:6px;">Nominal Cepat</div>
                <div class="quick-amounts-grid" id="quick-amounts"></div>

                <div class="change-box">
                    <span class="change-box-label">Kembalian</span>
                    <span class="change-box-value" id="change-display">Rp 0</span>
                </div>
            </div>

            <div style="display:flex; gap:12px; margin-top:24px;">
                <button id="cancel-payment" class="btn btn-ghost" style="flex:1;">Batal</button>
                <button id="confirm-payment" class="btn btn-success" style="flex:2; padding:12px; font-size:14px;">
                    ✓ Simpan & Selesaikan Transaksi
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL SUKSES --}}
    <div class="modal-overlay" id="success-modal">
        <div class="modal" style="text-align:center; max-width:440px;">
            <div
                style="width:64px; height:64px; border-radius:50%; background:rgba(16,185,129,0.15); color:var(--success); display:flex; align-items:center; justify-content:center; margin:0 auto 16px; font-size:32px; border:2px solid var(--success);">
                ✓
            </div>
            <div style="font-size:20px; font-weight:800; color:var(--text-primary); margin-bottom:6px;">
                Transaksi Berhasil!
            </div>
            <div style="font-size:13px; color:var(--text-muted); margin-bottom:20px;" id="success-invoice">
                Invoice: -
            </div>

            <div style="display:flex; flex-direction:column; gap:10px;">
                <button id="print-receipt" class="btn btn-primary"
                    style="padding:12px; justify-content:center; font-size:14px;">
                    🖨️ Cetak Struk Nota
                </button>
                <button id="new-transaction" class="btn btn-ghost" style="padding:10px; justify-content:center;">
                    + Transaksi Baru
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
        const PPN_ENABLED = {{ $ppnEnabled ? 'true' : 'false' }};
        const PPN_RATE = {{ $ppnPercentage }};
        const CURRENCY = '{{ $currencySymbol ?? \App\Models\Setting::get('currency_symbol', 'Rp') }}';
        const STORAGE_BASE = '{{ asset('storage') }}';

        let cart = [];
        let currentTransactionId = null;
        let activeCategory = '';

        // ===== FETCH PRODUCTS =====
        async function fetchProducts(categoryId = '', search = '') {
            const params = new URLSearchParams();
            if (categoryId) params.set('category_id', categoryId);
            if (search) params.set('search', search);

            try {
                const res = await fetch(`{{ route('pos.products') }}?${params}`);
                const products = await res.json();
                renderProducts(products);
            } catch (e) {
                document.getElementById('products-grid').innerHTML =
                    '<div style="grid-column:1/-1;text-align:center;padding:40px;color:var(--danger);">Gagal memuat produk. Coba lagi.</div>';
            }
        }

        // ===== RENDER PRODUCTS: FOTO, NAMA, KATEGORI, DAN HARGA =====
        function renderProducts(products) {
            const grid = document.getElementById('products-grid');
            if (!products.length) {
                grid.innerHTML =
                    '<div style="grid-column:1/-1;text-align:center;padding:40px;color:var(--text-muted);">Tidak ada produk yang cocok</div>';
                return;
            }

            grid.innerHTML = products.map(p => {
                const outOfStock = p.stock <= 0;
                const catName = p.category ? p.category.name : 'Umum';

                const imgHtml = p.image ?
                    `<img src="${STORAGE_BASE}/${p.image}" class="product-img" alt="${p.name}" loading="lazy" onerror="this.parentElement.innerHTML='<div class=\\'product-img-placeholder\\'><svg width=\\'28\\' height=\\'28\\' fill=\\'none\\' stroke=\\'currentColor\\' viewBox=\\'0 0 24 24\\'><path stroke-linecap=\\'round\\' stroke-linejoin=\\'round\\' stroke-width=\\'1.5\\' d=\\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\\'/></svg></div>'">` :
                    `<div class="product-img-placeholder">
                <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
               </div>`;

                return `
        <div class="product-card ${outOfStock ? 'out-of-stock' : ''}" ${!outOfStock ? `onclick="addToCart(${JSON.stringify(p).replace(/"/g, '&quot;')})"` : ''} title="${p.name}">
            <div class="product-img-wrapper">
                ${imgHtml}
                ${outOfStock ? '<span class="out-of-stock-pill">Habis</span>' : ''}
            </div>
            <span class="product-cat-tag">${catName}</span>
            <div class="product-name">${p.name}</div>
            <div class="product-price-row">
                <span class="product-price">Rp ${formatNumber(p.price)}</span>
                ${p.unit ? `<span class="product-unit">/${p.unit}</span>` : ''}
            </div>
        </div>`;
            }).join('');
        }

        // ===== CART =====
        function addToCart(product) {
            const existing = cart.find(i => i.id === product.id);
            if (existing) {
                if (existing.qty < product.stock) {
                    existing.qty++;
                } else {
                    alert(`Stok maksimal (${product.stock} ${product.unit}) tercapai!`);
                    return;
                }
            } else {
                cart.push({
                    ...product,
                    qty: 1
                });
            }
            renderCart();
            showAddAnimation();
        }

        function updateQty(productId, delta) {
            const item = cart.find(i => i.id === productId);
            if (!item) return;

            if (delta > 0 && item.qty >= item.stock) {
                alert(`Stok maksimal (${item.stock} ${item.unit}) tercapai!`);
                return;
            }

            item.qty += delta;
            if (item.qty <= 0) {
                cart = cart.filter(i => i.id !== productId);
            }
            renderCart();
        }

        function showAddAnimation() {
            const count = document.getElementById('cart-count');
            count.style.transform = 'scale(1.35)';
            setTimeout(() => count.style.transform = '', 180);
        }

        function renderCart() {
            const container = document.getElementById('cart-items');
            const count = document.getElementById('cart-count');
            const btn = document.getElementById('checkout-btn');

            const totalQty = cart.reduce((s, i) => s + i.qty, 0);
            count.textContent = totalQty;

            if (!cart.length) {
                container.innerHTML = `
            <div class="cart-empty" id="cart-empty">
                <div style="font-size:36px; opacity:0.6;">🛒</div>
                <div style="font-weight:600;">Keranjang masih kosong</div>
                <div style="font-size:12px;">Pilih produk di sisi kiri untuk memulai transaksi</div>
            </div>`;
                btn.disabled = true;
                updateSummary();
                return;
            }

            btn.disabled = false;
            container.innerHTML = cart.map(item => {
                const thumbHtml = item.image ?
                    `<img src="${STORAGE_BASE}/${item.image}" alt="${item.name}" loading="lazy" onerror="this.outerHTML='<div class=\\'cart-item-placeholder\\'><svg width=\\'16\\' height=\\'16\\' fill=\\'none\\' stroke=\\'currentColor\\' viewBox=\\'0 0 24 24\\'><path stroke-linecap=\\'round\\' stroke-linejoin=\\'round\\' stroke-width=\\'1.5\\' d=\\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\\'/></svg></div>'">` :
                    `<div class="cart-item-placeholder"><svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>`;

                return `
        <div class="cart-item" id="cart-item-${item.id}">
            <div class="cart-item-thumb">
                ${thumbHtml}
            </div>
            <div style="flex:1; min-width:0;">
                <div class="cart-item-name" title="${item.name}">${item.name}</div>
            </div>
            <div class="qty-control">
                <button type="button" class="qty-btn" onclick="updateQty(${item.id}, -1)">−</button>
                <span class="qty-val">${item.qty}</span>
                <button type="button" class="qty-btn" onclick="updateQty(${item.id}, 1)">+</button>
            </div>
            <div class="cart-item-sub">Rp ${formatNumber(item.price * item.qty)}</div>
            <span class="remove-item" onclick="removeItem(${item.id})" title="Hapus item">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </span>
        </div>`;
            }).join('');

            updateSummary();
        }

        function removeItem(productId) {
            cart = cart.filter(i => i.id !== productId);
            renderCart();
        }

        function updateSummary() {
            const subtotal = cart.reduce((s, i) => s + (parseFloat(i.price) * i.qty), 0);
            const discount = parseFloat(document.getElementById('discount-input').value) || 0;
            const taxable = Math.max(0, subtotal - discount);
            const tax = PPN_ENABLED ? (taxable * PPN_RATE / 100) : 0;
            const total = taxable + tax;

            document.getElementById('subtotal-display').textContent = 'Rp ' + formatNumber(subtotal);

            const discountRow = document.getElementById('discount-row');
            if (discount > 0) {
                discountRow.style.display = 'flex';
                document.getElementById('discount-display').textContent = '- Rp ' + formatNumber(discount);
            } else {
                discountRow.style.display = 'none';
            }

            document.getElementById('tax-display').textContent = 'Rp ' + formatNumber(tax);
            document.getElementById('total-display').textContent = 'Rp ' + formatNumber(total);
            document.getElementById('checkout-total-badge').textContent = 'Rp ' + formatNumber(total);
        }

        // ===== CHECKOUT =====
        document.getElementById('checkout-btn').addEventListener('click', () => {
            const total = getTotal();
            document.getElementById('modal-total').textContent = 'Rp ' + formatNumber(total);
            document.getElementById('payment-amount').value = '';
            document.getElementById('change-display').textContent = 'Rp 0';
            generateQuickAmounts(total);
            document.getElementById('payment-modal').classList.add('active');
            setTimeout(() => document.getElementById('payment-amount').focus(), 150);
        });

        document.getElementById('cancel-payment').addEventListener('click', () => {
            document.getElementById('payment-modal').classList.remove('active');
        });
        document.getElementById('cancel-payment-icon').addEventListener('click', () => {
            document.getElementById('payment-modal').classList.remove('active');
        });

        function getTotal() {
            const subtotal = cart.reduce((s, i) => s + (parseFloat(i.price) * i.qty), 0);
            const discount = parseFloat(document.getElementById('discount-input').value) || 0;
            const taxable = Math.max(0, subtotal - discount);
            const tax = PPN_ENABLED ? (taxable * PPN_RATE / 100) : 0;
            return taxable + tax;
        }

        function generateQuickAmounts(total) {
            const presets = [
                total,
                Math.ceil(total / 5000) * 5000,
                Math.ceil(total / 10000) * 10000,
                Math.ceil(total / 50000) * 50000,
                100000,
                200000
            ];
            const unique = [...new Set(presets.map(a => Math.ceil(a)))].filter(a => a >= total).slice(0, 6);
            document.getElementById('quick-amounts').innerHTML = unique.map((a, idx) =>
                `<button type="button" class="quick-amount-btn" onclick="setPaymentAmount(${a})">${idx === 0 ? 'Uang Pas' : 'Rp ' + formatNumber(a)}</button>`
            ).join('');
        }

        function setPaymentAmount(amount) {
            document.getElementById('payment-amount').value = amount;
            document.getElementById('payment-amount').dispatchEvent(new Event('input'));
        }

        document.getElementById('payment-amount').addEventListener('input', function() {
            const paid = parseFloat(this.value) || 0;
            const total = getTotal();
            const change = paid - total;
            const el = document.getElementById('change-display');
            el.textContent = 'Rp ' + formatNumber(Math.max(0, change));
            el.style.color = change >= 0 ? 'var(--success)' : 'var(--danger)';
        });

        document.getElementById('payment-method').addEventListener('change', function() {
            const isCash = this.value === 'cash';
            document.getElementById('cash-section').style.display = isCash ? 'block' : 'none';
        });

        document.getElementById('confirm-payment').addEventListener('click', async () => {
            const paymentMethod = document.getElementById('payment-method').value;
            const paymentAmount = paymentMethod === 'cash' ?
                parseFloat(document.getElementById('payment-amount').value) || 0 :
                getTotal();
            const total = getTotal();

            if (paymentMethod === 'cash' && paymentAmount < total) {
                alert('Jumlah bayar kurang dari total belanja!');
                document.getElementById('payment-amount').focus();
                return;
            }

            const btn = document.getElementById('confirm-payment');
            btn.disabled = true;
            btn.textContent = 'Memproses...';

            try {
                const res = await fetch('{{ route('pos.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    body: JSON.stringify({
                        customer_id: document.getElementById('customer-select').value || null,
                        items: cart.map(i => ({
                            product_id: i.id,
                            quantity: i.qty,
                            price: parseFloat(i.price)
                        })),
                        discount_amount: parseFloat(document.getElementById('discount-input')
                            .value) || 0,
                        payment_method: paymentMethod,
                        payment_amount: paymentAmount,
                    })
                });

                const data = await res.json();

                if (data.success) {
                    currentTransactionId = data.transaction_id;
                    document.getElementById('payment-modal').classList.remove('active');
                    document.getElementById('success-invoice').textContent = 'Invoice: ' + data.invoice_number;
                    document.getElementById('success-modal').classList.add('active');
                } else {
                    alert('Gagal: ' + (data.message || 'Terjadi kesalahan sistem'));
                }
            } catch (e) {
                alert('Terjadi kesalahan jaringan atau server.');
            } finally {
                btn.disabled = false;
                btn.textContent = '✓ Simpan & Selesaikan Transaksi';
            }
        });

        document.getElementById('print-receipt').addEventListener('click', () => {
            if (currentTransactionId) {
                const receiptUrl = '{{ route('transactions.receipt', '__ID__') }}'.replace('__ID__',
                    currentTransactionId);
                window.open(receiptUrl, '_blank');
            }
        });

        document.getElementById('new-transaction').addEventListener('click', () => {
            document.getElementById('success-modal').classList.remove('active');
            cart = [];
            document.getElementById('discount-input').value = '';
            renderCart();
            fetchProducts(activeCategory);
        });

        document.getElementById('clear-cart').addEventListener('click', () => {
            if (cart.length && confirm('Kosongkan semua produk di keranjang?')) {
                cart = [];
                renderCart();
            }
        });

        // ===== SEARCH & FILTER =====
        let searchTimer;
        document.getElementById('product-search').addEventListener('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => fetchProducts(activeCategory, this.value), 250);
        });

        document.querySelectorAll('.cat-chip').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.cat-chip').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                activeCategory = this.dataset.cat;
                fetchProducts(activeCategory, document.getElementById('product-search').value);
            });
        });

        document.getElementById('discount-input').addEventListener('input', updateSummary);

        // ===== UTILS =====
        function formatNumber(n) {
            return parseFloat(n).toLocaleString('id-ID', {
                maximumFractionDigits: 0
            });
        }

        // ===== INIT =====
        fetchProducts();
    </script>
@endpush
