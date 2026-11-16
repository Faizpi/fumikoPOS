<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faktur Penjualan A5 - {{ $penjualan->nomor ?? $penjualan->custom_number }}</title>
    <style>
        @page {
            size: A5 landscape;
            margin: 8mm 10mm;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, Helvetica, Arial, sans-serif;
            font-size: 11px;
            line-height: 1.35;
            color: #18181b;
            background: #f3f3f1;
            padding: 20px;
            font-variant-numeric: tabular-nums;
        }
        .toolbar {
            max-width: 210mm;
            margin: 0 auto 14px auto;
            background: #141416;
            color: #f8fafc;
            padding: 10px 16px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }
        .toolbar-title {
            font-size: 13px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .toolbar-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
        }
        .btn-primary {
            background: #d4af37;
            color: #0b0f17;
        }
        .btn-primary:hover {
            background: #c59b27;
        }
        .btn-secondary {
            background: #27272a;
            color: #f8fafc;
            border-color: #3f3f46;
        }
        .btn-secondary:hover {
            background: #3f3f46;
        }
        .a5-sheet {
            width: 210mm;
            min-height: 148mm;
            margin: 0 auto;
            background: #ffffff;
            padding: 10mm 12mm;
            border: 1px solid #d4d4d8;
            border-radius: 4px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .doc-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #18181b;
            padding-bottom: 8px;
            margin-bottom: 10px;
            gap: 16px;
        }
        .brand-box {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .brand-logo {
            width: 54px;
            height: 54px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
        }
        .brand-name {
            font-size: 16px;
            font-weight: 800;
            letter-spacing: 0.4px;
            color: #18181b;
            text-transform: uppercase;
        }
        .brand-sub {
            font-size: 10px;
            font-weight: 600;
            color: #7c5e10;
            margin-top: 1px;
        }
        .brand-meta {
            font-size: 9.5px;
            color: #475569;
            margin-top: 2px;
        }
        .doc-title-box {
            text-align: right;
        }
        .doc-type {
            font-size: 16px;
            font-weight: 800;
            letter-spacing: 0.8px;
            color: #18181b;
            text-transform: uppercase;
        }
        .doc-number {
            font-size: 12px;
            font-weight: 700;
            color: #7c5e10;
            margin-top: 2px;
        }
        .status-badge {
            display: inline-block;
            margin-top: 4px;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .status-lunas {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }
        .status-belum {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fcd34d;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1.25fr 1fr;
            gap: 12px;
            margin-bottom: 10px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 8px 10px;
        }
        .info-col h4 {
            font-size: 9.5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #475569;
            margin-bottom: 4px;
            border-bottom: 1px dashed #cbd5e1;
            padding-bottom: 2px;
        }
        .info-row {
            display: flex;
            font-size: 10.5px;
            margin-bottom: 2px;
        }
        .info-label {
            width: 95px;
            flex-shrink: 0;
            color: #475569;
            font-weight: 600;
        }
        .info-val {
            color: #18181b;
            font-weight: 600;
        }
        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        table.items-table th {
            background: #141416;
            color: #ffffff;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 5px 7px;
            border: 1px solid #18181b;
            text-align: left;
        }
        table.items-table td {
            padding: 5px 7px;
            border: 1px solid #cbd5e1;
            font-size: 10.5px;
            vertical-align: top;
        }
        table.items-table tr:nth-child(even) td {
            background: #f8fafc;
        }
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .font-bold { font-weight: 700; }
        .summary-footer {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 16px;
            align-items: start;
        }
        .notes-and-signs {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .memo-box {
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 6px 8px;
            font-size: 10px;
            background: #f8fafc;
            min-height: 36px;
        }
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 4px;
            text-align: center;
            font-size: 10px;
        }
        .sig-box {
            padding-top: 2px;
        }
        .sig-space {
            height: 38px;
        }
        .sig-line {
            border-top: 1px solid #18181b;
            padding-top: 3px;
            font-weight: 700;
        }
        .totals-box {
            border: 1px solid #18181b;
            border-radius: 4px;
            overflow: hidden;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 8px;
            font-size: 10.5px;
            border-bottom: 1px solid #e4e4e7;
        }
        .total-row.grand {
            background: #141416;
            color: #ffffff;
            font-size: 12px;
            font-weight: 800;
            padding: 6px 8px;
            border-bottom: none;
        }
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .toolbar {
                display: none !important;
            }
            .a5-sheet {
                width: 100%;
                min-height: auto;
                box-shadow: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    @php
        $subtotal = $penjualan->items->sum('jumlah_baris');
        $diskonAkhir = (float) ($penjualan->diskon_akhir ?? 0);
        $taxPct = (float) ($penjualan->tax_percentage ?? 0);
        $kenaPajak = max(0, $subtotal - $diskonAkhir);
        $pajakNominal = $kenaPajak * ($taxPct / 100);
        $ongkir = (float) ($penjualan->biaya_pengiriman ?? 0);
        $sudahBayar = (float) $penjualan->pembayarans->where('status', 'Approved')->sum('jumlah_bayar');
        if ($penjualan->status === 'Lunas' && $sudahBayar <= 0) {
            $sudahBayar = (float) $penjualan->grand_total;
        }
        $sisaTagihan = max(0, (float) $penjualan->grand_total - $sudahBayar);
        $statusPayment = $penjualan->status === 'Lunas' ? 'LUNAS' : ($penjualan->status === 'Approved' ? 'BELUM LUNAS' : strtoupper($penjualan->status));
    @endphp

    <div class="toolbar">
        <div class="toolbar-title">
            <span>Faktur / Nota Penjualan (Ukuran A5) - {{ $penjualan->nomor ?? $penjualan->custom_number }}</span>
        </div>
        <div class="toolbar-actions">
            <a href="{{ route('penjualan.print-surat-jalan-a5', $penjualan) }}" class="btn btn-secondary">
                Buka Surat Jalan (A5)
            </a>
            <button type="button" onclick="window.print()" class="btn btn-primary">
                Cetak Faktur A5
            </button>
        </div>
    </div>

    <div class="a5-sheet">
        <div>
            <div class="doc-header">
                <div class="brand-box">
                    <img src="{{ asset('assets/img/logo-fumiko.jpg') }}" alt="Fumiko Styrofoam" class="brand-logo">
                    <div>
                        <div class="brand-name">FUMIKO STYROFOAM</div>
                        
                        <div class="brand-meta">
                            {{ $penjualan->gudang?->nama_gudang ?? 'Gudang Utama Fumiko' }}
                            @if($penjualan->gudang?->alamat_gudang)
                                - {{ $penjualan->gudang->alamat_gudang }}
                            @endif
                        </div>
                    </div>
                </div>
                <div class="doc-title-box">
                    <div class="doc-type">FAKTUR / NOTA PENJUALAN</div>
                    <div class="doc-number">No: {{ $penjualan->nomor ?? $penjualan->custom_number }}</div>
                    <span class="status-badge {{ $penjualan->status === 'Lunas' ? 'status-lunas' : 'status-belum' }}">
                        Status Payment: {{ $statusPayment }}
                    </span>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-col">
                    <h4>Data Pembeli & Tujuan</h4>
                    <div class="info-row">
                        <span class="info-label">Nama Toko</span>
                        <span class="info-val">: {{ $penjualan->nama_toko ?: '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Nama Pembeli</span>
                        <span class="info-val">: {{ $penjualan->pelanggan ?: '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">No. HP / Telp</span>
                        <span class="info-val">: {{ $penjualan->no_telepon ? receipt_format_phone($penjualan->no_telepon) : '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Alamat</span>
                        <span class="info-val">: {{ $penjualan->alamat_penagihan ?: '-' }}</span>
                    </div>
                </div>
                <div class="info-col">
                    <h4>Detail Transaksi</h4>
                    <div class="info-row">
                        <span class="info-label">Tgl. Transaksi</span>
                        <span class="info-val">: {{ ($penjualan->tgl_transaksi ?? $penjualan->created_at)->format('d/m/Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Syarat Bayar</span>
                        <span class="info-val">: {{ $penjualan->syarat_pembayaran ?: 'Cash' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Jatuh Tempo</span>
                        <span class="info-val">: {{ $penjualan->tgl_jatuh_tempo ? $penjualan->tgl_jatuh_tempo->format('d/m/Y') : '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Sales / Admin</span>
                        <span class="info-val">: {{ $penjualan->user?->name ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 28px;" class="text-center">No</th>
                        <th>Nama Barang</th>
                        <th style="width: 115px;">Ukuran Pesanan</th>
                        <th style="width: 65px;" class="text-center">Qty</th>
                        <th style="width: 95px;" class="text-right">Harga Satuan</th>
                        <th style="width: 70px;" class="text-right">Diskon</th>
                        <th style="width: 105px;" class="text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($penjualan->items as $idx => $item)
                        @php
                            $ukuran = $item->ukuran ?: ($item->produk?->ukuran ?: '-');
                            $discText = '-';
                            if (($item->diskon ?? 0) > 0) {
                                $discText = ($item->diskon + 0) . '%';
                            } elseif (($item->diskon_nominal ?? 0) > 0) {
                                $discText = format_rupiah($item->diskon_nominal);
                            }
                        @endphp
                        <tr>
                            <td class="text-center">{{ $idx + 1 }}</td>
                            <td>
                                <div class="font-bold">{{ $item->produk?->nama_produk ?? $item->deskripsi ?? '-' }}</div>
                                @if($item->deskripsi && $item->produk)
                                    <div style="font-size: 9.5px; color: #475569;">{{ $item->deskripsi }}</div>
                                @endif
                            </td>
                            <td class="font-bold">{{ $ukuran }}</td>
                            <td class="text-center font-bold">{{ $item->kuantitas + 0 }} {{ $item->unit ?? $item->produk?->satuan ?? 'Pcs' }}</td>
                            <td class="text-right">{{ format_rupiah($item->harga_satuan) }}</td>
                            <td class="text-right">{{ $discText }}</td>
                            <td class="text-right font-bold">{{ format_rupiah($item->jumlah_baris) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="summary-footer">
            <div class="notes-and-signs">
                <div class="memo-box">
                    <strong>Catatan / Memo:</strong> {{ $penjualan->memo ?: 'Barang yang sudah diterima dalam kondisi baik tidak dapat dikembalikan.' }}
                </div>
                <div class="signatures">
                    <div class="sig-box">
                        <div>Penerima / Pembeli</div>
                        <div class="sig-space"></div>
                        <div class="sig-line">{{ $penjualan->nama_toko ?: ($penjualan->pelanggan ?: '( ............................ )') }}</div>
                    </div>
                    <div class="sig-box">
                        <div>Hormat Kami,</div>
                        <div class="sig-space"></div>
                        <div class="sig-line">Fumiko Styrofoam</div>
                    </div>
                </div>
            </div>

            <div class="totals-box">
                <div class="total-row">
                    <span>Subtotal</span>
                    <span class="font-bold">{{ format_rupiah($subtotal) }}</span>
                </div>
                @if($diskonAkhir > 0)
                    <div class="total-row">
                        <span>Diskon Akhir</span>
                        <span>- {{ format_rupiah($diskonAkhir) }}</span>
                    </div>
                @endif
                @if($pajakNominal > 0)
                    <div class="total-row">
                        <span>Pajak ({{ $taxPct + 0 }}%)</span>
                        <span>{{ format_rupiah($pajakNominal) }}</span>
                    </div>
                @endif
                @if($ongkir > 0)
                    <div class="total-row">
                        <span>Biaya Pengiriman</span>
                        <span>{{ format_rupiah($ongkir) }}</span>
                    </div>
                @endif
                <div class="total-row grand">
                    <span>GRAND TOTAL</span>
                    <span>{{ format_rupiah($penjualan->grand_total) }}</span>
                </div>
                <div class="total-row">
                    <span>Sudah Dibayar</span>
                    <span class="font-bold" style="color: #166534;">{{ format_rupiah($sudahBayar) }}</span>
                </div>
                <div class="total-row">
                    <span>Sisa Tagihan</span>
                    <span class="font-bold" style="color: {{ $sisaTagihan > 0 ? '#be123c' : '#166534' }};">{{ format_rupiah($sisaTagihan) }}</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
