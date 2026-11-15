<?php

namespace Database\Seeders;

use App\Accounting\MappingKey;
use App\Models\Account;
use App\Models\AccountMapping;
use App\Models\CashBankAccount;
use App\Models\Gudang;
use App\Models\GudangProduk;
use App\Models\Kontak;
use App\Models\Pembayaran;
use App\Models\Pembelian;
use App\Models\PembelianItem;
use App\Models\PenerimaanBarang;
use App\Models\PenerimaanBarangItem;
use App\Models\Penjualan;
use App\Models\PenjualanItem;
use App\Models\Produk;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use App\Models\User;
use App\Services\Accounting\HutangPostingService;
use App\Services\Accounting\PiutangPostingService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FumikoDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(HibiscusEfsyaChartOfAccountsSeeder::class);

        // === 1. Gudang Tunggal Fumiko ===
        $gudang = Gudang::firstOrCreate(
            ['nama_gudang' => 'Gudang Utama Fumiko'],
            [
                'alamat_gudang' => 'Jl. Raya Industri Cikupa No. 88, Tangerang',
            ]
        );

        // === 2. Demo Users (Super Admin, Admin, Sales) ===
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@fumiko.com'],
            [
                'name' => 'Super Admin Fumiko',
                'password' => Hash::make('password123'),
                'role' => 'super_admin',
                'no_telp' => '081100000001',
                'receives_transaction_email' => false,
                'can_export_pdf' => true,
                'can_export_excel' => true,
            ]
        );

        $admin = User::updateOrCreate(
            ['email' => 'admin@fumiko.com'],
            [
                'name' => 'Admin Gudang Fumiko',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'current_gudang_id' => $gudang->id,
                'gudang_id' => $gudang->id,
                'no_telp' => '081100000002',
                'receives_transaction_email' => false,
                'can_export_pdf' => true,
                'can_export_excel' => true,
            ]
        );
        $admin->gudangs()->sync([$gudang->id]);

        $sales = User::updateOrCreate(
            ['email' => 'sales@fumiko.com'],
            [
                'name' => 'Sales Fumiko',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'gudang_id' => $gudang->id,
                'no_telp' => '081100000003',
                'receives_transaction_email' => false,
            ]
        );

        // === 3. Account Mappings & Kas/Bank ===
        $this->seedAccountingMappings($superAdmin, $gudang);

        // === 4. Master Barang Fumiko (Bahan Dijual, Aset Perusahaan, Lain-lain) ===
        $products = [
            [
                'item_code' => 'FMK-BOX-75',
                'nama_produk' => 'Styrofoam Box AG-75 Tebal',
                'ukuran' => '75 x 42 x 32 cm',
                'kategori_barang' => 'bahan_jual',
                'satuan' => 'Pcs',
                'harga' => 48000,
                'harga_grosir' => 44000,
                'stok_penjualan' => 150,
            ],
            [
                'item_code' => 'FMK-BOX-50',
                'nama_produk' => 'Styrofoam Box AG-50 Garuda',
                'ukuran' => '52 x 37 x 34 cm',
                'kategori_barang' => 'bahan_jual',
                'satuan' => 'Pcs',
                'harga' => 36000,
                'harga_grosir' => 32500,
                'stok_penjualan' => 220,
            ],
            [
                'item_code' => 'FMK-SHT-18',
                'nama_produk' => 'Styrofoam Sheet Density 18',
                'ukuran' => '200 x 100 x 5 cm',
                'kategori_barang' => 'bahan_jual',
                'satuan' => 'Lembar',
                'harga' => 65000,
                'harga_grosir' => 59000,
                'stok_penjualan' => 95,
            ],
            [
                'item_code' => 'FMK-TRY-01',
                'nama_produk' => 'Styrofoam Tray & Lunch Box',
                'ukuran' => '20 x 15 x 7 cm',
                'kategori_barang' => 'bahan_jual',
                'satuan' => 'Pack',
                'harga' => 28000,
                'harga_grosir' => 25000,
                'stok_penjualan' => 310,
            ],
            [
                'item_code' => 'AST-CNC-01',
                'nama_produk' => 'Mesin Potong Kawat Panas',
                'ukuran' => '120 x 80 cm',
                'kategori_barang' => 'aset_perusahaan',
                'satuan' => 'Unit',
                'harga' => 4500000,
                'harga_grosir' => 4500000,
                'stok_penjualan' => 2,
            ],
            [
                'item_code' => 'AST-TRL-01',
                'nama_produk' => 'Troli Gudang Heavy Duty',
                'ukuran' => '90 x 60 cm',
                'kategori_barang' => 'aset_perusahaan',
                'satuan' => 'Unit',
                'harga' => 1250000,
                'harga_grosir' => 1250000,
                'stok_penjualan' => 4,
            ],
            [
                'item_code' => 'LLN-LKB-01',
                'nama_produk' => 'Lakban Bening Packing',
                'ukuran' => '48 mm x 90 yd',
                'kategori_barang' => 'lain_lain',
                'satuan' => 'Roll',
                'harga' => 12000,
                'harga_grosir' => 10500,
                'stok_penjualan' => 80,
            ],
            [
                'item_code' => 'LLN-WRP-01',
                'nama_produk' => 'Plastik Wrapping Pallet',
                'ukuran' => '50 cm x 300 m',
                'kategori_barang' => 'lain_lain',
                'satuan' => 'Roll',
                'harga' => 95000,
                'harga_grosir' => 88000,
                'stok_penjualan' => 18,
            ],
        ];

        $produkModels = [];
        foreach ($products as $item) {
            $stokQty = $item['stok_penjualan'];
            unset($item['stok_penjualan']);

            $produk = Produk::updateOrCreate(
                ['item_code' => $item['item_code']],
                $item
            );

            GudangProduk::updateOrCreate(
                ['gudang_id' => $gudang->id, 'produk_id' => $produk->id],
                [
                    'stok' => $stokQty,
                    'stok_penjualan' => $stokQty,
                    'stok_gratis' => 0,
                    'stok_sample' => 0,
                ]
            );

            $produkModels[$item['item_code']] = $produk;
        }

        // === 5. Kontak Customer & Supplier ===
        $custJaya = Kontak::updateOrCreate(
            ['kode_kontak' => 'KT00001'],
            [
                'nama_toko' => 'TB. Jaya Makmur Packaging',
                'nama' => 'Budi Santoso',
                'tipe_kontak' => 'pelanggan',
                'no_telp' => '081298765432',
                'pin' => '123456',
                'alamat' => 'Jl. Pasar Induk Kramat Jati Blok C-12, Jakarta Timur',
                'gudang_id' => $gudang->id,
                'created_by' => $sales->id,
            ]
        );

        $custSeafood = Kontak::updateOrCreate(
            ['kode_kontak' => 'KT00002'],
            [
                'nama_toko' => 'RM. Seafood Bahari Lestari',
                'nama' => 'Hendra Wijaya',
                'tipe_kontak' => 'pelanggan',
                'no_telp' => '081345678901',
                'pin' => '234567',
                'alamat' => 'Jl. Muara Baru Ujung No. 45, Jakarta Utara',
                'gudang_id' => $gudang->id,
                'created_by' => $sales->id,
            ]
        );

        $custKatering = Kontak::updateOrCreate(
            ['kode_kontak' => 'KT00003'],
            [
                'nama_toko' => 'Katering Berkah Nusantara',
                'nama' => 'Siti Rahmawati',
                'tipe_kontak' => 'pelanggan',
                'no_telp' => '085611223344',
                'pin' => '345678',
                'alamat' => 'Jl. Raya Serpong KM 8 No. 21, Tangerang Selatan',
                'gudang_id' => $gudang->id,
                'created_by' => $sales->id,
            ]
        );

        $supPolystyrene = Kontak::updateOrCreate(
            ['kode_kontak' => 'KT00004'],
            [
                'nama_toko' => 'PT. Polystyrene Indo Kimia',
                'nama' => 'Rudi Hartono',
                'tipe_kontak' => 'supplier',
                'no_telp' => '021-88997766',
                'pin' => '456789',
                'alamat' => 'Kawasan Industri Jababeka II Blok K-4, Cikarang',
                'gudang_id' => $gudang->id,
                'created_by' => $admin->id,
            ]
        );

        $supSumberPlastik = Kontak::updateOrCreate(
            ['kode_kontak' => 'KT00005'],
            [
                'nama_toko' => 'CV. Sumber Bahan Plastik Jaya',
                'nama' => 'Agus Setiawan',
                'tipe_kontak' => 'supplier',
                'no_telp' => '021-77665544',
                'pin' => '567890',
                'alamat' => 'Jl. Daan Mogot KM 14 No. 19, Jakarta Barat',
                'gudang_id' => $gudang->id,
                'created_by' => $admin->id,
            ]
        );

        if (Penjualan::exists()) {
            return;
        }

        // === 6. Transaksi Demo Barang Keluar, Tagihan di Luar & Uang Kas ===
        $box75 = $produkModels['FMK-BOX-75'];
        $box50 = $produkModels['FMK-BOX-50'];
        $sheet18 = $produkModels['FMK-SHT-18'];
        $tray01 = $produkModels['FMK-TRY-01'];

        // Invoice 1: Lunas (pembayaran masuk ke Uang Kas)
        $inv1 = Penjualan::create([
            'user_id' => $sales->id,
            'approver_id' => $admin->id,
            'gudang_id' => $gudang->id,
            'no_urut_harian' => 1,
            'nomor' => Penjualan::generateNomor($sales->id, 1, now()->subDays(5)),
            'tipe_harga' => 'grosir',
            'nama_toko' => $custJaya->nama_toko,
            'pelanggan' => $custJaya->nama,
            'no_telepon' => $custJaya->no_telp,
            'alamat_penagihan' => $custJaya->alamat,
            'tgl_transaksi' => now()->subDays(5)->toDateString(),
            'tgl_jatuh_tempo' => now()->addDays(9)->toDateString(),
            'syarat_pembayaran' => 'Net 14',
            'status' => 'Lunas',
            'diskon_akhir' => 0,
            'tax_percentage' => 0,
            'grand_total' => 2825000,
        ]);

        PenjualanItem::create([
            'penjualan_id' => $inv1->id,
            'produk_id' => $box75->id,
            'ukuran' => $box75->ukuran,
            'kuantitas' => 50,
            'unit' => $box75->satuan,
            'harga_satuan' => 44000,
            'diskon' => 0,
            'jumlah_baris' => 2200000,
        ]);
        PenjualanItem::create([
            'penjualan_id' => $inv1->id,
            'produk_id' => $tray01->id,
            'ukuran' => $tray01->ukuran,
            'kuantitas' => 25,
            'unit' => $tray01->satuan,
            'harga_satuan' => 25000,
            'diskon' => 0,
            'jumlah_baris' => 625000,
        ]);

        $pay1 = Pembayaran::create([
            'type' => 'piutang',
            'user_id' => $admin->id,
            'approver_id' => $superAdmin->id,
            'gudang_id' => $gudang->id,
            'penjualan_id' => $inv1->id,
            'no_urut_harian' => 1,
            'nomor' => Pembayaran::generateNomor($admin->id, 1, now()->subDays(4)),
            'tgl_pembayaran' => now()->subDays(4)->toDateString(),
            'metode_pembayaran' => 'Transfer',
            'jumlah_bayar' => 2825000,
            'keterangan' => 'Pelunasan invoice '.$inv1->nomor,
            'status' => 'Approved',
        ]);

        // Invoice 2: Tagihan di Luar - Belum Lunas (DP masuk Uang Kas 1.000.000, sisa 1.520.000)
        $inv2 = Penjualan::create([
            'user_id' => $sales->id,
            'approver_id' => $admin->id,
            'gudang_id' => $gudang->id,
            'no_urut_harian' => 2,
            'nomor' => Penjualan::generateNomor($sales->id, 2, now()->subDays(2)),
            'tipe_harga' => 'retail',
            'nama_toko' => $custSeafood->nama_toko,
            'pelanggan' => $custSeafood->nama,
            'no_telepon' => $custSeafood->no_telp,
            'alamat_penagihan' => $custSeafood->alamat,
            'tgl_transaksi' => now()->subDays(2)->toDateString(),
            'tgl_jatuh_tempo' => now()->addDays(12)->toDateString(),
            'syarat_pembayaran' => 'Net 14',
            'status' => 'Approved',
            'diskon_akhir' => 0,
            'tax_percentage' => 0,
            'grand_total' => 2520000,
        ]);

        PenjualanItem::create([
            'penjualan_id' => $inv2->id,
            'produk_id' => $box75->id,
            'ukuran' => $box75->ukuran,
            'kuantitas' => 30,
            'unit' => $box75->satuan,
            'harga_satuan' => 48000,
            'diskon' => 0,
            'jumlah_baris' => 1440000,
        ]);
        PenjualanItem::create([
            'penjualan_id' => $inv2->id,
            'produk_id' => $box50->id,
            'ukuran' => $box50->ukuran,
            'kuantitas' => 30,
            'unit' => $box50->satuan,
            'harga_satuan' => 36000,
            'diskon' => 0,
            'jumlah_baris' => 1080000,
        ]);

        $pay2 = Pembayaran::create([
            'type' => 'piutang',
            'user_id' => $admin->id,
            'approver_id' => $superAdmin->id,
            'gudang_id' => $gudang->id,
            'penjualan_id' => $inv2->id,
            'no_urut_harian' => 2,
            'nomor' => Pembayaran::generateNomor($admin->id, 2, now()->subDay()),
            'tgl_pembayaran' => now()->subDay()->toDateString(),
            'metode_pembayaran' => 'Cash',
            'jumlah_bayar' => 1000000,
            'keterangan' => 'DP tunai dari '.$custSeafood->nama_toko,
            'status' => 'Approved',
        ]);

        // Invoice 3: Tagihan di Luar - Belum dibayar sama sekali (1.800.000)
        $inv3 = Penjualan::create([
            'user_id' => $sales->id,
            'approver_id' => $admin->id,
            'gudang_id' => $gudang->id,
            'no_urut_harian' => 1,
            'nomor' => Penjualan::generateNomor($sales->id, 1, now()),
            'tipe_harga' => 'retail',
            'nama_toko' => $custKatering->nama_toko,
            'pelanggan' => $custKatering->nama,
            'no_telepon' => $custKatering->no_telp,
            'alamat_penagihan' => $custKatering->alamat,
            'tgl_transaksi' => now()->toDateString(),
            'tgl_jatuh_tempo' => now()->addDays(7)->toDateString(),
            'syarat_pembayaran' => 'Net 7',
            'status' => 'Approved',
            'diskon_akhir' => 0,
            'tax_percentage' => 0,
            'grand_total' => 1800000,
        ]);

        PenjualanItem::create([
            'penjualan_id' => $inv3->id,
            'produk_id' => $box50->id,
            'ukuran' => $box50->ukuran,
            'kuantitas' => 50,
            'unit' => $box50->satuan,
            'harga_satuan' => 36000,
            'diskon' => 0,
            'jumlah_baris' => 1800000,
        ]);

        // Post piutang journals so ledger mutations & aging stay consistent
        try {
            $piutangService = app(PiutangPostingService::class);
            $piutangService->postSale($inv1, $superAdmin->id);
            $piutangService->postPayment($pay1, (string) $pay1->jumlah_bayar, null, $superAdmin->id);
            $piutangService->postSale($inv2, $superAdmin->id);
            $piutangService->postPayment($pay2, (string) $pay2->jumlah_bayar, null, $superAdmin->id);
            $piutangService->postSale($inv3, $superAdmin->id);
        } catch (\Throwable $e) {
            // Safe fallback
        }

        // === 7. Surat Jalan Supplier Masuk Gudang & Hutang Supplier ===
        $po1 = Pembelian::create([
            'user_id' => $admin->id,
            'approver_id' => $superAdmin->id,
            'gudang_id' => $gudang->id,
            'kontak_id' => $supPolystyrene->id,
            'no_urut_harian' => 1,
            'nomor' => Pembelian::generateNomor($admin->id, 1, now()->subDays(3)),
            'no_surat_jalan' => 'SJ-PIK/2026/0801',
            'tipe_harga' => 'grosir',
            'urgensi' => 'Sedang',
            'syarat_pembayaran' => 'Net 30',
            'tgl_transaksi' => now()->subDays(3)->toDateString(),
            'tgl_jatuh_tempo' => now()->addDays(27)->toDateString(),
            'status' => 'Approved',
            'grand_total' => 6500000,
        ]);

        PembelianItem::create([
            'pembelian_id' => $po1->id,
            'produk_id' => $sheet18->id,
            'ukuran' => $sheet18->ukuran,
            'kuantitas' => 100,
            'unit' => $sheet18->satuan,
            'harga_satuan' => 55000,
            'diskon' => 0,
            'jumlah_baris' => 5500000,
        ]);
        PembelianItem::create([
            'pembelian_id' => $po1->id,
            'produk_id' => $tray01->id,
            'ukuran' => $tray01->ukuran,
            'kuantitas' => 50,
            'unit' => $tray01->satuan,
            'harga_satuan' => 20000,
            'diskon' => 0,
            'jumlah_baris' => 1000000,
        ]);

        $pb1 = PenerimaanBarang::create([
            'user_id' => $admin->id,
            'approver_id' => $superAdmin->id,
            'gudang_id' => $gudang->id,
            'pembelian_id' => $po1->id,
            'no_urut_harian' => 1,
            'nomor' => PenerimaanBarang::generateNomor($admin->id, 1, now()->subDays(3)),
            'tgl_penerimaan' => now()->subDays(3)->toDateString(),
            'no_surat_jalan' => 'SJ-PIK/2026/0801',
            'status' => 'Approved',
            'keterangan' => 'Barang bahan baku diterima lengkap sesuai surat jalan supplier.',
        ]);

        PenerimaanBarangItem::create([
            'penerimaan_barang_id' => $pb1->id,
            'produk_id' => $sheet18->id,
            'qty_diterima' => 100,
            'qty_reject' => 0,
            'tipe_stok' => 'penjualan',
        ]);
        PenerimaanBarangItem::create([
            'penerimaan_barang_id' => $pb1->id,
            'produk_id' => $tray01->id,
            'qty_diterima' => 50,
            'qty_reject' => 0,
            'tipe_stok' => 'penjualan',
        ]);

        try {
            app(HutangPostingService::class)->postPurchase($po1, $superAdmin->id);
        } catch (\Throwable $e) {
            // Safe fallback
        }

        // === 8. Demo Stock Opname ===
        $sop = StockOpname::create([
            'user_id' => $admin->id,
            'approver_id' => $superAdmin->id,
            'gudang_id' => $gudang->id,
            'no_urut_harian' => 1,
            'nomor' => StockOpname::generateNomor($admin->id, 1, now()->subDay()),
            'tgl_opname' => now()->subDay()->toDateString(),
            'status' => 'Approved',
            'memo' => 'Pengecekan harian stok bahan dijual & aset gudang Fumiko.',
        ]);

        StockOpnameItem::create([
            'stock_opname_id' => $sop->id,
            'produk_id' => $box75->id,
            'qty_system' => 150,
            'qty_aktual' => 150,
            'keterangan' => 'Sesuai fisik rak A1',
        ]);
        StockOpnameItem::create([
            'stock_opname_id' => $sop->id,
            'produk_id' => $produkModels['AST-CNC-01']->id,
            'qty_system' => 2,
            'qty_aktual' => 2,
            'keterangan' => 'Mesin operasional baik',
        ]);
    }

    private function seedAccountingMappings(User $superAdmin, Gudang $gudang): void
    {
        $codeMap = [
            MappingKey::SalesRetailRevenue->value => ['code' => '4-1100', 'section' => 'Penjualan'],
            MappingKey::SalesDiscount->value => ['code' => '4-1910', 'section' => 'Penjualan'],
            MappingKey::ArReceivable->value => ['code' => '1-1200', 'section' => 'Piutang'],
            MappingKey::PurchaseInventory->value => ['code' => '1-1300', 'section' => 'Pembelian'],
            MappingKey::ApPayable->value => ['code' => '2-1100', 'section' => 'Hutang'],
            MappingKey::CashInTransit->value => ['code' => '1-1120', 'section' => 'Kas & Bank'],
            MappingKey::CashDefault->value => ['code' => '1-1101', 'section' => 'Kas & Bank'],
            MappingKey::BankDefault->value => ['code' => '1-1111', 'section' => 'Kas & Bank'],
            MappingKey::CashRounding->value => ['code' => '4-1300', 'section' => 'Kas & Bank'],
        ];

        foreach (MappingKey::runtimeRequired() as $key) {
            $meta = $codeMap[$key->value] ?? null;
            if (! $meta) {
                continue;
            }
            $account = Account::where('code', $meta['code'])->first();
            if (! $account) {
                continue;
            }

            AccountMapping::firstOrCreate(
                ['mapping_key' => $key->value, 'effective_from' => '2026-01-01'],
                [
                    'section' => $meta['section'],
                    'account_id' => $account->id,
                    'is_active' => true,
                    'is_protected' => true,
                    'changed_by' => $superAdmin->id,
                    'change_reason' => 'Initial Fumiko POS mapping',
                ]
            );
        }

        if ($kasAcc = Account::where('code', '1-1101')->first()) {
            CashBankAccount::firstOrCreate(
                ['account_id' => $kasAcc->id],
                [
                    'name' => 'Kas Utama Fumiko',
                    'type' => 'kas',
                    'gudang_id' => $gudang->id,
                    'is_active' => true,
                ]
            );
        }

        if ($bankAcc = Account::where('code', '1-1111')->first()) {
            CashBankAccount::firstOrCreate(
                ['account_id' => $bankAcc->id],
                [
                    'name' => 'Bank BCA Fumiko',
                    'type' => 'bank',
                    'gudang_id' => $gudang->id,
                    'bank_name' => 'BCA',
                    'bank_account_holder' => 'Fumiko Styrofoam',
                    'is_active' => true,
                ]
            );
        }
    }
}
