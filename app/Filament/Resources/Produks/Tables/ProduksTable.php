<?php

namespace App\Filament\Resources\Produks\Tables;

use App\Models\Produk;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProduksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->summaries(false, false)
            ->columns([
                TextColumn::make('item_code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Kode barang disalin')
                    ->placeholder('-')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('nama_produk')
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap(),

                TextColumn::make('ukuran')
                    ->label('Ukuran')
                    ->searchable()
                    ->placeholder('-')
                    ->badge()
                    ->color('info'),

                TextColumn::make('kategori_barang')
                    ->label('Kategori')
                    ->formatStateUsing(fn (?string $state): string => Produk::KATEGORI_OPTIONS[$state ?? 'bahan_jual'] ?? 'Bahan Dijual (Produk)')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'aset_perusahaan' => 'warning',
                        'lain_lain' => 'gray',
                        default => 'success',
                    }),

                TextColumn::make('satuan')
                    ->label('Satuan')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('harga')
                    ->label('Harga Jual')
                    ->money('IDR')
                    ->sortable()
                    ->alignRight(),

                TextColumn::make('harga_grosir')
                    ->label('Harga Grosir')
                    ->money('IDR')
                    ->sortable()
                    ->alignRight()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('stok_di_gudang_sum_stok')
                    ->label('Total Stok')
                    ->sum('stokDiGudang', 'stok')
                    ->numeric()
                    ->badge()
                    ->color('primary'),
            ])
            ->filters([
                SelectFilter::make('kategori_barang')
                    ->label('Kategori Gudang')
                    ->options(Produk::KATEGORI_OPTIONS),
                SelectFilter::make('satuan')
                    ->options([
                        'Pcs' => 'Pcs',
                        'Bal' => 'Bal',
                        'Koli' => 'Koli',
                        'Lembar' => 'Lembar',
                        'Box' => 'Box',
                        'Unit' => 'Unit',
                    ]),
            ])
            ->defaultSort('nama_produk')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum ada data barang')
            ->emptyStateDescription('Tambahkan barang styrofoam, aset perusahaan, atau perlengkapan gudang.')
            ->emptyStateIcon('heroicon-o-cube');
    }
}
