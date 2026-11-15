<?php

namespace Tests\Feature;

use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\HutangPage;
use App\Filament\Pages\PiutangPage;
use App\Filament\Resources\Gudangs\GudangResource;
use App\Models\Gudang;
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
}
