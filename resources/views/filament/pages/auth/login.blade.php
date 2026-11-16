<div class="he-auth-root">
<style>
    body:has(.he-auth-root) {
        overflow-x: hidden;
        background: #f8fafc;
    }

    .dark body:has(.he-auth-root),
    body.dark:has(.he-auth-root) {
        background: #090c10;
    }

    .fi-simple-layout {
        min-height: 100svh !important;
        padding: 0 !important;
        background: transparent !important;
        border-radius: 0 !important;
    }

    .dark .fi-simple-layout {
        background: transparent !important;
    }

    .fi-simple-main-ctn,
    .fi-simple-main {
        display: block !important;
        width: 100% !important;
        max-width: none !important;
        min-height: 100svh !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .he-auth-root {
        --fm-gold: #c9a227;
        --fm-gold-light: #e5c158;
        --fm-gold-dark: #7c5e10;
        --fm-ink-on-gold: #0b0f17;
        --fm-panel-bg: #ffffff;
        --fm-panel-text: #0f172a;
        --fm-muted: #475569;
        --fm-border: #cbd5e1;
        --fm-input-bg: #ffffff;
        --fm-placeholder: #64748b;
        --fm-demo-bg: #f8fafc;
        --fm-demo-hover: #fef9e7;
        width: 100%;
        min-height: 100svh;
        background: var(--fm-panel-bg);
        color: var(--fm-panel-text);
    }

    .dark .he-auth-root {
        --fm-gold-dark: #e5c158;
        --fm-panel-bg: #0b0f17;
        --fm-panel-text: #f8fafc;
        --fm-muted: #cbd5e1;
        --fm-border: #334155;
        --fm-input-bg: #111827;
        --fm-placeholder: #94a3b8;
        --fm-demo-bg: #111827;
        --fm-demo-hover: #1c1917;
    }

    .he-auth-shell {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(400px, 1fr);
        width: 100%;
        min-height: 100svh;
        background: var(--fm-panel-bg);
    }

    .he-auth-brand {
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 36px;
        min-width: 0;
        padding: 48px 56px;
        overflow: hidden;
        background: radial-gradient(circle at top right, #231d0f 0%, #0d1016 58%, #07090d 100%);
        color: #f8fafc;
        border-right: 1px solid rgba(201, 162, 39, .28);
    }

    .he-auth-brand::before {
        content: "";
        position: absolute;
        inset: 0;
        background:
            linear-gradient(rgba(201, 162, 39, .08) 1px, transparent 1px),
            linear-gradient(90deg, rgba(201, 162, 39, .08) 1px, transparent 1px);
        background-size: 40px 40px;
        mask-image: linear-gradient(135deg, rgba(0, 0, 0, .75), transparent 72%);
        pointer-events: none;
    }

    .he-auth-brand > * {
        position: relative;
        z-index: 1;
    }

    .he-brand-mark,
    .he-mobile-logo {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .he-brand-mark img {
        width: 120px;
        height: 68px;
        border-radius: 10px;
        background: #090b0f;
        object-fit: cover;
        border: 1px solid rgba(229, 193, 88, .48);
        box-shadow: 0 14px 32px rgba(0, 0, 0, .4);
    }

    .he-brand-name {
        color: #f8fafc;
        font-size: 20px;
        font-weight: 800;
        line-height: 1.15;
    }

    .he-auth-copy {
        max-width: 540px;
    }

    .he-auth-title {
        max-width: 540px;
        margin: 0;
        color: #ffffff;
        font-size: 44px;
        font-weight: 850;
        line-height: 1.08;
    }

    .he-auth-text {
        max-width: 500px;
        margin: 18px 0 0;
        color: #e2e8f0;
        font-size: 15px;
        line-height: 1.65;
    }

    .he-auth-form-side {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 0;
        padding: 36px 40px;
        background: var(--fm-panel-bg);
    }

    .he-auth-panel {
        width: min(100%, 460px);
    }

    .he-mobile-logo {
        display: none;
        margin-bottom: 20px;
    }

    .he-mobile-brand-name {
        color: var(--fm-panel-text);
    }

    .he-form-eyebrow {
        color: var(--fm-gold-dark);
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .he-form-title {
        margin: 8px 0 0;
        color: var(--fm-panel-text);
        font-size: 28px;
        font-weight: 850;
        line-height: 1.16;
    }

    .he-form-subtitle {
        margin: 8px 0 20px;
        color: var(--fm-muted);
        font-size: 14px;
        line-height: 1.5;
    }

    .he-login-card {
        padding: 0;
    }

    .he-login-card .fi-fo {
        gap: 14px;
    }

    .he-login-card .fi-fo-field-wrp-label,
    .he-login-card .fi-fo-field-wrp-label span,
    .he-login-card label {
        color: var(--fm-panel-text) !important;
        opacity: 1 !important;
        visibility: visible !important;
    }

    .he-login-card .fi-fo-field-wrp-label .fi-fo-field-wrp-required-mark {
        color: #dc2626 !important;
    }

    .he-login-card .fi-fo-field-wrp-error-message {
        color: #dc2626 !important;
    }

    .he-login-card .fi-input-wrp {
        border: 1px solid var(--fm-border) !important;
        border-radius: 8px !important;
        background: var(--fm-input-bg) !important;
    }

    .he-login-card .fi-input-wrp:focus-within {
        border-color: var(--fm-gold) !important;
        box-shadow: 0 0 0 3px rgba(201, 162, 39, .22) !important;
    }

    .he-login-card .fi-input,
    .he-login-card input {
        color: var(--fm-panel-text) !important;
        caret-color: var(--fm-gold-dark);
        background: transparent !important;
    }

    .he-login-card .fi-input::placeholder,
    .he-login-card input::placeholder {
        color: var(--fm-placeholder) !important;
        opacity: 1;
    }

    .he-login-card .fi-icon-btn,
    .he-login-card .fi-input-wrp-icon {
        color: var(--fm-muted) !important;
    }

    .he-login-card .fi-checkbox-input {
        border-color: var(--fm-border) !important;
        background: var(--fm-input-bg) !important;
    }

    .he-login-card .fi-checkbox-input:checked {
        border-color: var(--fm-gold) !important;
        background-color: var(--fm-gold) !important;
    }

    .he-login-card .fi-btn,
    .he-login-card .fi-btn.fi-color-primary {
        min-height: 44px;
        border: 1px solid #b58e1c !important;
        border-radius: 8px !important;
        background: linear-gradient(135deg, #d4af37 0%, #f0cf65 55%, #c9a227 100%) !important;
        color: var(--fm-ink-on-gold) !important;
        font-weight: 800;
        box-shadow: 0 8px 18px rgba(201, 162, 39, .28) !important;
    }

    .he-login-card .fi-btn:hover,
    .he-login-card .fi-btn.fi-color-primary:hover {
        background: linear-gradient(135deg, #c59b27 0%, #e5c158 55%, #b58e1c 100%) !important;
    }

    .he-login-card .fi-btn-label {
        color: var(--fm-ink-on-gold) !important;
    }

    .fm-demo-box {
        margin-top: 22px;
        padding-top: 18px;
        border-top: 1px solid var(--fm-border);
    }

    .fm-demo-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 10px;
    }

    .fm-demo-title {
        color: var(--fm-panel-text);
        font-size: 13px;
        font-weight: 800;
    }

    .fm-demo-badge {
        padding: 3px 10px;
        border-radius: 999px;
        background: linear-gradient(135deg, #d4af37, #e5c158);
        color: var(--fm-ink-on-gold);
        font-size: 11px;
        font-weight: 800;
    }

    .fm-demo-list {
        display: grid;
        gap: 8px;
    }

    .fm-demo-btn {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        width: 100%;
        min-height: 48px;
        padding: 10px 12px;
        border: 1px solid var(--fm-border);
        border-radius: 8px;
        background: var(--fm-demo-bg);
        color: var(--fm-panel-text);
        text-align: left;
        cursor: pointer;
        transition: border-color .15s ease, background-color .15s ease;
    }

    .fm-demo-btn:hover,
    .fm-demo-btn:focus-visible {
        border-color: var(--fm-gold);
        background: var(--fm-demo-hover);
        outline: 2px solid var(--fm-gold);
        outline-offset: 1px;
    }

    .fm-demo-role {
        font-size: 13px;
        font-weight: 800;
        color: var(--fm-panel-text);
        line-height: 1.25;
    }

    .fm-demo-meta {
        margin-top: 2px;
        font-size: 11.5px;
        color: var(--fm-muted);
        line-height: 1.35;
    }

    .fm-demo-cta {
        flex: none;
        padding: 5px 12px;
        border-radius: 6px;
        background: linear-gradient(135deg, #d4af37, #e5c158);
        color: var(--fm-ink-on-gold);
        font-size: 11px;
        font-weight: 800;
    }

    @media (max-width: 1280px) {
        .he-auth-brand {
            padding: 36px 40px;
        }

        .he-auth-title {
            font-size: 38px;
        }
    }

    @media (max-width: 920px) {
        .he-auth-shell {
            grid-template-columns: 1fr;
        }

        .he-auth-brand {
            padding: 28px 24px;
        }

        .he-auth-title {
            font-size: 32px;
        }

        .he-auth-form-side {
            padding: 28px 20px;
        }
    }

    @media (max-width: 680px) {
        .he-auth-brand {
            display: none;
        }

        .he-mobile-logo {
            display: flex;
        }

        .he-form-title {
            font-size: 24px;
        }
    }
</style>

<div class="he-auth-shell">
    <section class="he-auth-brand" aria-label="Fumiko POS">
        <div class="he-brand-mark">
            <img src="{{ asset('assets/img/logo-fumiko.jpg') }}" alt="Logo Fumiko Styrofoam">
            <div>
                <div class="he-brand-name">Fumiko POS</div>
            </div>
        </div>

        <div class="he-auth-copy">
            <h1 class="he-auth-title">Kontrol Gudang, Tagihan & Uang Kas Fumiko.</h1>
            <p class="he-auth-text">
                Sistem operasional terpadu untuk pencatatan barang keluar, cetak Faktur & Surat Jalan ukuran A5, stock opname gudang, pemantauan tagihan di luar, hingga hutang surat jalan supplier.
            </p>
        </div>
    </section>

    <section class="he-auth-form-side" aria-label="Form login Fumiko POS">
        <div class="he-auth-panel">
            <div class="he-mobile-logo">
                <span style="display: inline-block; width: 0.45rem; height: 1.5rem; border-radius: 999px; background: linear-gradient(180deg, #e5c158 0%, #b8860b 100%);"></span>
                <div>
                    <div class="he-brand-name he-mobile-brand-name">Fumiko POS</div>
                </div>
            </div>

            <div class="he-form-eyebrow">Akses Sistem Operasional</div>
            <h2 class="he-form-title">Masuk ke Fumiko POS</h2>
            <p class="he-form-subtitle">Gunakan akun staff atau pilih shortcut role demo di bawah untuk mencoba sistem langsung.</p>

            <div class="he-login-card">
                {{ $this->content }}
            </div>

            <div class="fm-demo-box" aria-label="Shortcut role demo">
                <div class="fm-demo-heading">
                    <span class="fm-demo-title">Shortcut Login Demo</span>
                    <span class="fm-demo-badge">3 Role Aktif</span>
                </div>

                <div class="fm-demo-list">
                    @foreach ($this->getDemoAccounts() as $demo)
                        <button
                            type="button"
                            wire:click="loginAsDemo('{{ $demo['key'] }}')"
                            wire:loading.attr="disabled"
                            class="fm-demo-btn"
                        >
                            <div>
                                <div class="fm-demo-role">{{ $demo['label'] }} · <span style="font-weight:600;">{{ $demo['role_label'] }}</span></div>
                                <div class="fm-demo-meta">{{ $demo['email'] }} / {{ $demo['password'] }} - {{ $demo['hint'] }}</div>
                            </div>
                            <span class="fm-demo-cta">Masuk</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</div>

<x-filament-actions::modals />
</div>
