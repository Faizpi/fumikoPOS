<?php

namespace Tests\Feature;

use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\HutangPage;
use App\Filament\Pages\PiutangPage;
use App\Filament\Resources\Gudangs\GudangResource;
use App\Filament\Resources\Pembayarans\Pages\CreatePembayaran;
use App\Filament\Resources\Penjualans\Pages\CreatePenjualan;
use App\Filament\Resources\Penjualans\Pages\ViewPenjualan;
use App\Models\Gudang;
use App\Models\GudangProduk;
use App\Models\Pembayaran;
use App\Models\Pembelian;
use App\Models\PenerimaanBarang;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\User;
use Database\Seeders\FumikoDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FumikoFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_fumiko_demo_seeder_and_single_warehouse_lock(): void
    {
        $this->seed(FumikoDemoSeeder::class);

        $gudang = Gudang::where('nama_gudang', 'Gudang Utama Fumiko')->firstOrFail();

        $superAdmin = User::where('email', 'superadmin@fumiko.com')->firstOrFail();
        $admin = User::where('email', 'admin@fumiko.com')->firstOrFail();
        $sales = User::where('email', 'sales@fumiko.com')->firstOrFail();

        $this->assertTrue($superAdmin->isSuperAdmin());
        $this->assertTrue($admin->isAdmin());
        $this->assertSame('user', $sales->role);

        $this->actingAs($superAdmin);
        $this->assertFalse(GudangResource::canCreate());
        $this->assertSame(Gudang::count() > 1, GudangResource::canDelete($gudang));

        $this->assertGreaterThan(0, Produk::where('kategori_barang', 'bahan_jual')->count());
        $this->assertGreaterThan(0, Produk::where('kategori_barang', 'aset_perusahaan')->count());
        $this->assertGreaterThan(0, Produk::where('kategori_barang', 'lain_lain')->count());
    }

    public function test_login_page_and_demo_role_shortcuts(): void
    {
        $this->seed(FumikoDemoSeeder::class);

        $this->get('/app/login')
            ->assertOk()
            ->assertSee('Fumiko POS')
            ->assertSee('Shortcut Login Demo')
            ->assertSee('superadmin@fumiko.com')
            ->assertSee('admin@fumiko.com')
            ->assertSee('sales@fumiko.com');

        Livewire::test(Login::class)
            ->call('loginAsDemo', 'super_admin')
            ->assertRedirect();
        $this->assertAuthenticatedAs(User::where('email', 'superadmin@fumiko.com')->firstOrFail());

        auth()->logout();

        Livewire::test(Login::class)
            ->call('loginAsDemo', 'admin')
            ->assertRedirect();
        $this->assertAuthenticatedAs(User::where('email', 'admin@fumiko.com')->firstOrFail());

        auth()->logout();

        Livewire::test(Login::class)
            ->call('loginAsDemo', 'sales')
            ->assertRedirect();
        $this->assertAuthenticatedAs(User::where('email', 'sales@fumiko.com')->firstOrFail());
    }

    public function test_a5_print_routes_for_faktur_and_surat_jalan(): void
    {
        $this->seed(FumikoDemoSeeder::class);

        $admin = User::where('email', 'admin@fumiko.com')->firstOrFail();
        $penjualan = Penjualan::with('items')->firstOrFail();
        $penerimaan = PenerimaanBarang::firstOrFail();

        $this->actingAs($admin);

        $this->get(route('penjualan.print-faktur-a5', $penjualan->id))
            ->assertOk()
            ->assertSee('FUMIKO STYROFOAM')
            ->assertSee('size: A5 landscape')
            ->assertSee($penjualan->nama_toko)
            ->assertSee($penjualan->items->first()->ukuran);

        $this->get(route('penjualan.print-surat-jalan-a5', $penjualan->id))
            ->assertOk()
            ->assertSee('SURAT JALAN')
            ->assertSee('size: A5 landscape')
            ->assertSee($penjualan->nama_toko);

        $this->get(route('penerimaan-barang.print-surat-jalan-a5', $penerimaan->id))
            ->assertOk()
            ->assertSee('SURAT JALAN MASUK GUDANG')
            ->assertSee($penerimaan->no_surat_jalan);
    }

    public function test_piutang_and_hutang_pages_show_customer_and_supplier_surat_jalan(): void
    {
        $this->seed(FumikoDemoSeeder::class);

        $admin = User::where('email', 'admin@fumiko.com')->firstOrFail();
        $this->actingAs($admin);

        $this->assertTrue(HutangPage::canAccess());

        $piutangComponent = new PiutangPage;
        $piutangComponent->mount();
        $piutangRows = $piutangComponent->getListToko()->getCollection();
        $this->assertTrue($piutangRows->contains(fn ($row) => ! empty($row['nama_toko'])));

        $hutangComponent = new HutangPage;
        $hutangComponent->mount();
        $hutangRows = $hutangComponent->getListTempo()->getCollection();
        $pembelian = Pembelian::firstOrFail();
        $this->assertTrue($hutangRows->contains(fn ($row) => $row['no_surat_jalan'] === $pembelian->no_surat_jalan));
    }

    public function test_all_core_filament_pages_render_and_end_to_end_sale_payment_flow_works(): void
    {
        $this->seed(FumikoDemoSeeder::class);

        $admin = User::where('email', 'admin@fumiko.com')->firstOrFail();
        $gudang = Gudang::where('nama_gudang', 'Gudang Utama Fumiko')->firstOrFail();
        $produk = Produk::where('item_code', 'FMK-BOX-75')->firstOrFail();

        $this->actingAs($admin);

        // 1. Pastikan seluruh halaman inti Fumiko POS bisa dibuka oleh Admin (HTTP 200)
        foreach ([
            '/app',
            '/app/penjualans',
            '/app/penjualans/create',
            '/app/pembayarans',
            '/app/pembayarans/create',
            '/app/pembelians',
            '/app/pembelians/create',
            '/app/penerimaan-barangs',
            '/app/penerimaan-barangs/create',
            '/app/stock-opnames',
            '/app/stock-opnames/create',
            '/app/stok-page',
            '/app/piutang-page',
            '/app/hutang-page',
            '/app/produks',
            '/app/kontaks',
        ] as $url) {
            $response = $this->get($url);
            $this->assertSame(200, $response->status(), "Admin gagal akses {$url}");
        }

        $stokAwal = (int) GudangProduk::where('gudang_id', $gudang->id)
            ->where('produk_id', $produk->id)
            ->value('stok_penjualan');

        // 2. Buat transaksi Barang Keluar via Filament CreatePenjualan
        Livewire::test(CreatePenjualan::class)
            ->fillForm([
                'gudang_id' => $gudang->id,
                'tipe_harga' => 'retail',
                'syarat_pembayaran' => 'Net 7',
                'tgl_transaksi' => now()->toDateString(),
                'nama_toko' => 'TB. Jaya Makmur Packaging',
                'pelanggan' => 'Budi Santoso',
                'no_telepon' => '081234567899',
                'alamat_penagihan' => 'Jl. Merdeka No. 10',
                'items' => [
                    [
                        'produk_id' => $produk->id,
                        'ukuran' => '75 x 42 x 32 cm',
                        'kuantitas' => 5,
                        'unit' => 'Pcs',
                        'harga_satuan' => 48000,
                        'diskon' => 0,
                        'jumlah_baris' => 240000,
                    ],
                ],
                'diskon_akhir' => 0,
                'tax_percentage' => 0,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $penjualan = Penjualan::where('nama_toko', 'TB. Jaya Makmur Packaging')->latest('id')->firstOrFail();
        $this->assertSame('Pending', $penjualan->status);
        $this->assertSame('75 x 42 x 32 cm', $penjualan->items()->firstOrFail()->ukuran);

        // 3. Approve transaksi Barang Keluar -> stok berkurang otomatis
        Livewire::test(ViewPenjualan::class, ['record' => $penjualan->getRouteKey()])
            ->callAction('approve');

        $penjualan->refresh();
        $this->assertSame('Approved', $penjualan->status);
        $stokSesudah = (int) GudangProduk::where('gudang_id', $gudang->id)
            ->where('produk_id', $produk->id)
            ->value('stok_penjualan');
        $this->assertSame($stokAwal - 5, $stokSesudah);

        // 4. Input Pembayaran (Uang Kas) -> langsung Approved & invoice otomatis Lunas
        Livewire::test(CreatePembayaran::class)
            ->fillForm([
                'gudang_id' => $gudang->id,
                'penjualan_ids' => [$penjualan->id],
                'tgl_pembayaran' => now()->toDateString(),
                'metode_pembayaran' => 'Cash',
                'jumlah_bayar' => 240000,
                'keterangan' => 'Pelunasan uji coba',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $penjualan->refresh();
        $this->assertSame('Lunas', $penjualan->status);
        $this->assertSame(
            'Approved',
            Pembayaran::where('penjualan_id', $penjualan->id)->firstOrFail()->status
        );
    }
}
