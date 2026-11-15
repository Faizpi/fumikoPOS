<?php

namespace App\Filament\Resources\Penjualans\Tables;

use App\Filament\Concerns\TransactionDeleteGuard;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PenjualansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->summaries(false, false)
            ->columns([
                TextColumn::make('tgl_transaksi')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->description(fn ($record) => $record->created_at?->format('H:i'))
                    ->sortable(),

                TextColumn::make('nomor')
                    ->label('No. Faktur')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),

                TextColumn::make('nama_toko')
                    ->label('Nama Toko')
                    ->searchable()
                    ->weight('bold')
                    ->placeholder('-'),

                TextColumn::make('pelanggan')
                    ->label('Nama Pembeli')
                    ->searchable(),

                TextColumn::make('no_telepon')
                    ->label('No. HP')
                    ->searchable()
                    ->placeholder('-')
                    ->formatStateUsing(fn ($state) => \receipt_format_phone($state)),

                TextColumn::make('items_summary')
                    ->label('Barang & Ukuran')
                    ->state(function ($record): string {
                        return $record->items->map(function ($item) {
                            $ukuran = $item->ukuran ?: $item->produk?->ukuran;
                            $label = $item->produk?->nama_produk ?? '-';

                            return $ukuran
                                ? "{$label} ({$ukuran}) x{$item->kuantitas}"
                                : "{$label} x{$item->kuantitas}";
                        })->implode(', ');
                    })
                    ->limit(45)
                    ->tooltip(fn ($state): string => (string) $state),

                TextColumn::make('syarat_pembayaran')
                    ->label('Syarat Bayar')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('grand_total')
                    ->label('Total Tagihan')
                    ->money('IDR')
                    ->sortable()
                    ->alignRight()
                    ->weight('bold'),

                TextColumn::make('status_display')
                    ->label('Status Payment')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'Belum Lunas' => 'danger',
                        'Lunas' => 'success',
                        'Canceled' => 'gray',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Payment')
                    ->options([
                        'Pending' => 'Pending (Menunggu)',
                        'Approved' => 'Belum Lunas (Tagihan Luar)',
                        'Lunas' => 'Sudah Dibayar (Lunas)',
                        'Canceled' => 'Dibatalkan',
                    ]),
                SelectFilter::make('syarat_pembayaran')
                    ->label('Syarat Pembayaran')
                    ->options([
                        'Cash' => 'Cash / Tunai',
                        'Net 7' => 'Tempo 7 Hari',
                        'Net 14' => 'Tempo 14 Hari',
                        'Net 30' => 'Tempo 30 Hari',
                        'Net 60' => 'Tempo 60 Hari',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('fakturA5')
                    ->label('Faktur A5')
                    ->icon('heroicon-o-document-text')
                    ->color('primary')
                    ->url(fn ($record) => route('penjualan.print-faktur-a5', $record))
                    ->openUrlInNewTab(),
                Action::make('suratJalanA5')
                    ->label('Surat Jalan A5')
                    ->icon('heroicon-o-truck')
                    ->color('info')
                    ->url(fn ($record) => route('penjualan.print-surat-jalan-a5', $record))
                    ->openUrlInNewTab(),
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn ($record) => auth()->user()?->isSuperAdmin() && ! in_array($record->status, ['Approved', 'Lunas'], true)),
                DeleteAction::make()
                    ->visible(fn ($record): bool => auth()->user()?->isSuperAdmin() && TransactionDeleteGuard::canDeletePenjualan($record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => false),
                ]),
            ])
            ->emptyStateHeading('Belum ada transaksi barang keluar')
            ->emptyStateDescription('Buat transaksi barang keluar baru untuk mencetak Surat Jalan & Faktur A5.')
            ->emptyStateIcon('heroicon-o-shopping-cart');
    }
}
