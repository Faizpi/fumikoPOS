<?php

namespace App\Filament\Resources\Pembayarans\Widgets;

use App\Filament\Resources\Pembayarans\PembayaranResource;
use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UangKasStatsWidget extends BaseStatsOverviewWidget
{
    protected function getColumns(): int|array
    {
        return 3;
    }

    protected function getStats(): array
    {
        $baseQuery = PembayaranResource::getEloquentQuery();

        $approvedTotal = (float) $baseQuery->clone()->where('status', 'Approved')->sum('jumlah_bayar');
        $cashTotal = (float) $baseQuery->clone()->where('status', 'Approved')->where('metode_pembayaran', 'Cash')->sum('jumlah_bayar');
        $nonCashTotal = max(0, $approvedTotal - $cashTotal);

        return [
            Stat::make('Total Uang Kas Masuk', format_rupiah($approvedTotal))
                ->description('Pembayaran customer berstatus Approved')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make('Kas Tunai (Cash)', format_rupiah($cashTotal))
                ->description('Penerimaan tunai langsung')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('primary'),

            Stat::make('Transfer / QRIS / Lainnya', format_rupiah($nonCashTotal))
                ->description('Penerimaan rekening & non-tunai')
                ->descriptionIcon('heroicon-o-credit-card')
                ->color('info'),
        ];
    }
}
