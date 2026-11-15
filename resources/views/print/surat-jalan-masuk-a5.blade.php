<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan Masuk Gudang A5 - {{ $penerimaan->no_surat_jalan ?: $penerimaan->nomor }}</title>
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
            color: #0f172a;
            background: #e2e8f0;
            padding: 20px;
            font-variant-numeric: tabular-nums;
        }
        .toolbar {
            max-width: 210mm;
            margin: 0 auto 14px auto;
            background: #0f172a;
            color: #f8fafc;
            padding: 10px 16px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
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
            border: none;
            background: #d97706;
            color: #ffffff;
        }
        .a5-sheet {
            width: 210mm;
            min-height: 148mm;
            margin: 0 auto;
            background: #ffffff;
            padding: 10mm 12mm;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.12);
            border-radius: 4px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .doc-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 10px;
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
            color: #0f172a;
        }
        .brand-sub {
            font-size: 10px;
            font-weight: 600;
            color: #b45309;
        }
        .doc-title-box {
            text-align: right;
        }
        .doc-type {
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
        }
        .doc-number {
            font-size: 12px;
            font-weight: 700;
            color: #b45309;
            margin-top: 2px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 10px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 8px 10px;
        }
        .info-row {
            display: flex;
            font-size: 10.5px;
            margin-bottom: 2px;
        }
        .info-label {
            width: 110px;
            flex-shrink: 0;
            color: #475569;
            font-weight: 600;
        }
        .info-val {
            color: #0f172a;
            font-weight: 600;
        }
        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        table.items-table th {
            background: #0f172a;
            color: #ffffff;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 5px 7px;
            border: 1px solid #0f172a;
            text-align: left;
        }
        table.items-table td {
            padding: 6px 7px;
            border: 1px solid #cbd5e1;
            font-size: 10.5px;
        }
        .text-center { text-align: center !important; }
        .font-bold { font-weight: 700; }
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 16px;
            text-align: center;
            font-size: 10px;
            margin-top: 10px;
        }
        .sig-space { height: 40px; }
        .sig-line {
            border-top: 1px solid #0f172a;
            padding-top: 3px;
            font-weight: 700;
        }
        @media print {
            body { background: #ffffff; padding: 0; }
            .toolbar { display: none !important; }
            .a5-sheet { width: 100%; min-height: auto; box-shadow: none; padding: 0; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <div><strong>Bukti Surat Jalan Masuk Gudang (A5)</strong> - {{ $penerimaan->no_surat_jalan ?: $penerimaan->nomor }}</div>
        <button type="button" onclick="window.print()" class="btn">Cetak A5</button>
    </div>

    <div class="a5-sheet">
        <div>
            <div class="doc-header">
                <div class="brand-box">
                    <img src="{{ asset('assets/img/logo-fumiko.jpg') }}" alt="Fumiko Styrofoam" class="brand-logo">
                    <div>
                        <div class="brand-name">FUMIKO STYROFOAM</div>
                        <div class="brand-sub">Bukti Penerimaan Surat Jalan Supplier</div>
                    </div>
                </div>
                <div class="doc-title-box">
                    <div class="doc-type">SURAT JALAN MASUK GUDANG</div>
                    <div class="doc-number">No. SJ Supplier: {{ $penerimaan->no_surat_jalan ?: '-' }}</div>
                    <div style="font-size: 10px; color: #475569;">No. Sistem: {{ $penerimaan->nomor }}</div>
                </div>
            </div>

            <div class="info-grid">
                <div>
                    <div class="info-row">
                        <span class="info-label">Supplier</span>
                        <span class="info-val">: {{ $penerimaan->pembelian?->kontak?->nama ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">No. Order / Tagihan</span>
                        <span class="info-val">: {{ $penerimaan->pembelian?->nomor ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">No. Surat Jalan</span>
                        <span class="info-val">: {{ $penerimaan->no_surat_jalan ?: ($penerimaan->pembelian?->no_surat_jalan ?: '-') }}</span>
                    </div>
                </div>
                <div>
                    <div class="info-row">
                        <span class="info-label">Tgl. Masuk Gudang</span>
                        <span class="info-val">: {{ ($penerimaan->tgl_penerimaan ?? $penerimaan->created_at)->format('d/m/Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Gudang Tujuan</span>
                        <span class="info-val">: {{ $penerimaan->gudang?->nama_gudang ?? 'Gudang Utama Fumiko' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Petugas Penerima</span>
                        <span class="info-val">: {{ $penerimaan->user?->name ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 30px;" class="text-center">No</th>
                        <th style="width: 90px;">Kode</th>
                        <th>Nama Barang</th>
                        <th style="width: 130px;">Ukuran</th>
                        <th style="width: 85px;" class="text-center">Qty Diterima</th>
                        <th style="width: 75px;" class="text-center">Qty Reject</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($penerimaan->items as $idx => $item)
                        <tr>
                            <td class="text-center">{{ $idx + 1 }}</td>
                            <td>{{ $item->produk?->item_code ?: '-' }}</td>
                            <td class="font-bold">{{ $item->produk?->nama_produk ?? '-' }}</td>
                            <td>{{ $item->produk?->ukuran ?: '-' }}</td>
                            <td class="text-center font-bold">{{ $item->qty_diterima + 0 }} {{ $item->produk?->satuan ?? 'Pcs' }}</td>
                            <td class="text-center">{{ $item->qty_reject + 0 }}</td>
                            <td>{{ $item->keterangan ?: 'Sesuai Surat Jalan' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div>
            <div style="border: 1px solid #cbd5e1; padding: 6px 8px; border-radius: 4px; background: #f8fafc; font-size: 10px;">
                <strong>Catatan Gudang:</strong> {{ $penerimaan->keterangan ?: 'Barang telah diterima dan diverifikasi sesuai Surat Jalan Supplier.' }}
            </div>
            <div class="signatures">
                <div>
                    <div>Pengirim / Supplier</div>
                    <div class="sig-space"></div>
                    <div class="sig-line">{{ $penerimaan->pembelian?->kontak?->nama ?? '( ............................ )' }}</div>
                </div>
                <div>
                    <div>Petugas Gudang</div>
                    <div class="sig-space"></div>
                    <div class="sig-line">{{ $penerimaan->user?->name ?? '( ............................ )' }}</div>
                </div>
                <div>
                    <div>Disetujui Oleh</div>
                    <div class="sig-space"></div>
                    <div class="sig-line">{{ $penerimaan->approver?->name ?? 'Admin Fumiko' }}</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
