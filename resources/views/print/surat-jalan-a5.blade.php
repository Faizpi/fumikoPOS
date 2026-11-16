<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan A5 - {{ $penjualan->nomor ?? $penjualan->custom_number }}</title>
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
            font-size: 17px;
            font-weight: 800;
            letter-spacing: 1px;
            color: #18181b;
            text-transform: uppercase;
        }
        .doc-number {
            font-size: 12px;
            font-weight: 700;
            color: #7c5e10;
            margin-top: 2px;
        }
        .doc-ref {
            font-size: 10px;
            color: #475569;
            margin-top: 2px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1.3fr 1fr;
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
            padding: 6px 7px;
            border: 1px solid #cbd5e1;
            font-size: 10.5px;
            vertical-align: middle;
        }
        table.items-table tr:nth-child(even) td {
            background: #f8fafc;
        }
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .font-bold { font-weight: 700; }
        .footer-section {
            margin-top: 6px;
        }
        .memo-bar {
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 6px 8px;
            font-size: 10px;
            background: #f8fafc;
            margin-bottom: 10px;
        }
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 16px;
            text-align: center;
            font-size: 10px;
        }
        .sig-space {
            height: 42px;
        }
        .sig-line {
            border-top: 1px solid #18181b;
            padding-top: 3px;
            font-weight: 700;
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
        $invNo = $penjualan->nomor ?? $penjualan->custom_number;
        $sjNo = str_starts_with((string) $invNo, 'INV-') ? 'SJ-' . substr((string) $invNo, 4) : 'SJ-' . $invNo;
        $statusPayment = $penjualan->status === 'Lunas' ? 'LUNAS' : ($penjualan->status === 'Approved' ? 'BELUM LUNAS' : strtoupper($penjualan->status));
        $totalQty = $penjualan->items->sum('kuantitas');
    @endphp

    <div class="toolbar">
        <div class="toolbar-title">
            Surat Jalan Barang Keluar (Ukuran A5) - {{ $sjNo }}
        </div>
        <div class="toolbar-actions">
            <a href="{{ route('penjualan.print-faktur-a5', $penjualan) }}" class="btn btn-secondary">
                Buka Faktur / Nota (A5)
            </a>
            <button type="button" onclick="window.print()" class="btn btn-primary">
                Cetak Surat Jalan A5
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
                    <div class="doc-type">SURAT JALAN</div>
                    <div class="doc-number">No: {{ $sjNo }}</div>
                    <div class="doc-ref">Ref Faktur: {{ $invNo }} | Status: <strong>{{ $statusPayment }}</strong></div>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-col">
                    <h4>Kepada Yth. (Tujuan Pengiriman)</h4>
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
                        <span class="info-label">Alamat Kirim</span>
                        <span class="info-val">: {{ $penjualan->alamat_penagihan ?: '-' }}</span>
                    </div>
                </div>
                <div class="info-col">
                    <h4>Informasi Pengiriman</h4>
                    <div class="info-row">
                        <span class="info-label">Tanggal Kirim</span>
                        <span class="info-val">: {{ ($penjualan->tgl_transaksi ?? $penjualan->created_at)->format('d/m/Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Gudang Asal</span>
                        <span class="info-val">: {{ $penjualan->gudang?->nama_gudang ?? 'Gudang Utama Fumiko' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status Payment</span>
                        <span class="info-val">: {{ $statusPayment }} ({{ $penjualan->syarat_pembayaran ?: 'Cash' }})</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">No. Resi / Ref</span>
                        <span class="info-val">: {{ $penjualan->no_resi ?: ($penjualan->no_referensi ?: '-') }}</span>
                    </div>
                </div>
            </div>

            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 30px;" class="text-center">No</th>
                        <th style="width: 90px;">Kode Barang</th>
                        <th>Nama Barang</th>
                        <th style="width: 140px;">Ukuran Pesanan</th>
                        <th style="width: 75px;" class="text-center">Jumlah</th>
                        <th style="width: 65px;" class="text-center">Satuan</th>
                        <th style="width: 130px;">Keterangan / Cek</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($penjualan->items as $idx => $item)
                        @php
                            $ukuran = $item->ukuran ?: ($item->produk?->ukuran ?: '-');
                        @endphp
                        <tr>
                            <td class="text-center">{{ $idx + 1 }}</td>
                            <td>{{ $item->produk?->item_code ?: '-' }}</td>
                            <td class="font-bold">{{ $item->produk?->nama_produk ?? $item->deskripsi ?? '-' }}</td>
                            <td class="font-bold">{{ $ukuran }}</td>
                            <td class="text-center font-bold">{{ $item->kuantitas + 0 }}</td>
                            <td class="text-center">{{ $item->unit ?? $item->produk?->satuan ?? 'Pcs' }}</td>
                            <td>{{ $item->deskripsi ?: 'Baik / Sesuai' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-right font-bold">TOTAL BARANG DIKIRIM</td>
                        <td class="text-center font-bold">{{ $totalQty + 0 }}</td>
                        <td colspan="2" class="font-bold">Item / Kemasan</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="footer-section">
            <div class="memo-bar">
                <strong>Catatan Pengiriman:</strong> {{ $penjualan->memo ?: 'Harap periksa kesesuaian nama barang, ukuran, dan jumlah sebelum menandatangani Surat Jalan ini.' }}
            </div>
            <div class="signatures">
                <div>
                    <div>Penerima / Toko</div>
                    <div class="sig-space"></div>
                    <div class="sig-line">{{ $penjualan->nama_toko ?: ($penjualan->pelanggan ?: '( ............................ )') }}</div>
                </div>
                <div>
                    <div>Pengirim / Sopir</div>
                    <div class="sig-space"></div>
                    <div class="sig-line">( ............................ )</div>
                </div>
                <div>
                    <div>Hormat Kami (Gudang)</div>
                    <div class="sig-space"></div>
                    <div class="sig-line">Fumiko Styrofoam</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
