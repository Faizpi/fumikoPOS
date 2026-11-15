<?php

namespace App\Filament\Widgets;

use App\Models\Biaya;
use App\Models\GudangProduk;
use App\Models\Kunjungan;
use App\Models\Pembayaran;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\Produk;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOperasional extends BaseStatsOverviewWidget
{
    protected ?string $heading = 'Informasi Operasional';

    protected function getColumns(): int|array
    {
        return 4;
    }

    protected function getStats(): array
    {
        $user = auth()->user();
        $isSuperAdmin = $user?->isSuperAdmin();
        $isAdmin = $user?->isAdmin();
        $isUser = ! $isSuperAdmin && ! $isAdmin;

        $gudangId = $isAdmin ? $user?->getCurrentGudang()?->id : null;
        $userId = $isUser ? $user->id : null;

        $scope = function ($query, ?string $gudangColumn = 'gudang_id', ?string $userColumn = 'user_id') use ($isSuperAdmin, $isAdmin, $gudangId, $userId) {
            if ($isSuperAdmin) {
                return $query;
            }
            if ($isAdmin && $gudangId && $gudangColumn) {
                return $query->where($gudangColumn, $gudangId);
            }
            if ($userId && $userColumn) {
                return $query->where($userColumn, $userId);
            }

            return $query->whereRaw('1=0');
        };

        $now = Carbon::now();
        $bulanLabel = $now->translatedFormat('F Y');
        $bulanQuery = fn ($q) => $q->whereYear('tgl_transaksi', $now->year)->whereMonth('tgl_transaksi', $now->month);

        // 1. Menunggu Approval (Pending)
        $pendingBreakdown = $isUser ? [
            Penjualan::where('user_id', $userId)->where('status', 'Pending')->count(),
            Pembelian::where('user_id', $userId)->where('status', 'Pending')->count(),
            Biaya::where('user_id', $userId)->where('status', 'Pending')->count(),
            Kunjungan::where('user_id', $userId)->where('status', 'Pending')->count(),
            Pembayaran::where('user_id', $userId)->where('status', 'Pending')->count(),
        ] : [
            $scope(Penjualan::where('status', 'Pending'))->count(),
            $scope(Pembelian::where('status', 'Pending'))->count(),
            $scope(Biaya::where('status', 'Pending'))->count(),
            $scope(Kunjungan::where('status', 'Pending'), 'gudang_id', 'user_id')->count(),
            $scope(Pembayaran::where('status', 'Pending'))->count(),
        ];
        $menungguApproval = array_sum($pendingBreakdown);

        // 2. Transaksi Batal (Canceled)
        $canceled = $scope($bulanQuery(Penjualan::query())->where('status', 'Canceled'))->count()
            + $scope($bulanQuery(Pembelian::query())->where('status', 'Canceled'))->count()
            + $scope($bulanQuery(Biaya::query())->where('status', 'Canceled'))->count()
            + $scope(Kunjungan::whereYear('tgl_kunjungan', $now->year)->whereMonth('tgl_kunjungan', $now->month)->where('status', 'Canceled'), 'gudang_id', 'user_id')->count();

        // 3. Tagihan di Luar (Piutang Belum Lunas) & Hutang Supplier
        $piutangQuery = $scope(
            Penjualan::query()
                ->where('status', 'Approved')
                ->withSum(['pembayarans as paid_total' => fn ($q) => $q->where('status', 'Approved')], 'jumlah_bayar')
        );
        $tagihanDiLuar = (float) $piutangQuery->get(['id', 'grand_total'])->sum(
            fn ($p) => max(0, (float) $p->grand_total - (float) ($p->paid_total ?? 0))
        );

        $hutangQuery = $scope(
            Pembelian::query()->where('status', 'Approved')
        );
        $hutangSupplier = (float) $hutangQuery->get(['id', 'grand_total'])->sum(function ($pb) {
            $paid = (float) Pembayaran::where('type', 'hutang')->where('pembelian_id', $pb->id)->where('status', 'Approved')->sum('jumlah_bayar');

            return max(0, (float) $pb->grand_total - $paid);
        });

        // 4. Total Produk
        if ($isUser && $userId) {
            $gudangUser = $user?->getCurrentGudang();
            $totalProduk = $gudangUser ? GudangProduk::where('gudang_id', $gudangUser->id)->count() : 0;
            $produkLabel = 'Stok Barang Gudang';
        } elseif ($isAdmin && $gudangId) {
            $totalProduk = GudangProduk::where('gudang_id', $gudangId)->count();
            $produkLabel = 'Stok Barang Gudang';
        } else {
            $totalProduk = Produk::count();
            $produkLabel = 'Total Item Gudang';
        }

        return [
            Stat::make('Tagihan di Luar (Belum Lunas)', format_rupiah($tagihanDiLuar))
                ->description('Piutang customer aktif')
                ->descriptionIcon('heroicon-o-document-currency-dollar')
                ->color($tagihanDiLuar > 0 ? 'warning' : 'success'),

            Stat::make('Hutang ke Supplier', format_rupiah($hutangSupplier))
                ->description('Sesuai surat jalan masuk')
                ->descriptionIcon('heroicon-o-truck')
                ->color($hutangSupplier > 0 ? 'danger' : 'success'),

            Stat::make('Menunggu Approval', number_format($menungguApproval))
                ->description('Pending transaksi · '.$bulanLabel)
                ->descriptionIcon('heroicon-o-clock')
                ->color($menungguApproval > 0 ? 'warning' : 'success'),

            Stat::make($produkLabel, number_format($totalProduk))
                ->description('Bahan jual, aset & lain-lain')
                ->descriptionIcon('heroicon-o-cube')
                ->color('gray'),
        ];
    }
}
