<?php

namespace App\Filament\Resources\Kontaks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class KontaksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->summaries(false, false)
            ->columns([
                TextColumn::make('kode_kontak')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->copyable()
                    ->placeholder('-'),

                TextColumn::make('nama_toko')
                    ->label('Nama Toko')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->placeholder('-'),

                TextColumn::make('nama')
                    ->label('Nama Pembeli / Kontak')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tipe_kontak')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'supplier' => 'Supplier',
                        'keduanya' => 'Customer & Supplier',
                        default => 'Customer',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'supplier' => 'warning',
                        'keduanya' => 'info',
                        default => 'success',
                    }),

                TextColumn::make('no_telp')
                    ->label('No. HP')
                    ->searchable()
                    ->copyable()
                    ->placeholder('-')
                    ->icon('heroicon-o-phone')
                    ->formatStateUsing(fn ($state) => \receipt_format_phone($state)),

                TextColumn::make('alamat')
                    ->label('Alamat')
                    ->searchable()
                    ->limit(40)
                    ->placeholder('-'),

                TextColumn::make('gudang.nama_gudang')
                    ->label('Gudang')
                    ->placeholder('-')
                    ->badge()
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('diskon_persen')
                    ->label('Diskon')
                    ->suffix('%')
                    ->alignRight()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('creator.name')
                    ->label('Dibuat oleh')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('tipe_kontak')
                    ->label('Tipe Kontak')
                    ->options([
                        'customer' => 'Customer / Pembeli',
                        'supplier' => 'Supplier / Pemasok',
                        'keduanya' => 'Customer & Supplier',
                    ]),
            ])
            ->defaultSort('nama')
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
            ->emptyStateHeading('Belum ada kontak')
            ->emptyStateDescription('Tambahkan data pembeli/toko atau supplier untuk mulai transaksi.')
            ->emptyStateIcon('heroicon-o-identification');
    }
}
