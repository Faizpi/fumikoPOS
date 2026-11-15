<?php

namespace App\Filament\Resources\Produks\Schemas;

use App\Models\Produk;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProdukForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 1, 'md' => 2])
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Informasi Barang & Ukuran')
                            ->description('Master data barang styrofoam dijual, aset perusahaan, maupun perlengkapan lain-lain.')
                            ->icon('heroicon-o-cube')
                            ->schema([
                                TextInput::make('nama_produk')
                                    ->label('Nama Barang / Bahan')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull()
                                    ->placeholder('Contoh: Box Styrofoam AG / Lembaran EPS'),

                                TextInput::make('ukuran')
                                    ->label('Ukuran / Dimensi')
                                    ->maxLength(100)
                                    ->placeholder('Contoh: 40 x 60 x 30 cm')
                                    ->helperText('Ukuran akan tampil otomatis di transaksi, Faktur A5 & Surat Jalan A5'),

                                Select::make('kategori_barang')
                                    ->label('Kategori Barang Gudang')
                                    ->options(Produk::KATEGORI_OPTIONS)
                                    ->default('bahan_jual')
                                    ->required()
                                    ->native(false),

                                TextInput::make('item_code')
                                    ->label('Kode Barang (SKU)')
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->placeholder('Contoh: FMK-BOX-4060')
                                    ->helperText('Biarkan kosong jika tidak ada SKU'),

                                Select::make('satuan')
                                    ->label('Satuan')
                                    ->required()
                                    ->options([
                                        'Pcs' => 'Pcs',
                                        'Bal' => 'Bal',
                                        'Koli' => 'Koli',
                                        'Lembar' => 'Lembar',
                                        'Box' => 'Box',
                                        'Unit' => 'Unit',
                                        'Roll' => 'Roll',
                                        'Kg' => 'Kg',
                                    ])
                                    ->default('Pcs')
                                    ->searchable()
                                    ->native(false),
                            ])
                            ->columns(['default' => 1, 'md' => 2]),

                        Section::make('Harga & Keterangan')
                            ->description('Harga jual retail dan grosir (isi 0 jika item merupakan aset/pendukung internal).')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                TextInput::make('harga')
                                    ->label('Harga Jual (Retail)')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->minValue(0),

                                TextInput::make('harga_grosir')
                                    ->label('Harga Grosir')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->minValue(0)
                                    ->helperText('Berlaku jika tipe harga "Grosir" dipilih saat transaksi'),

                                Textarea::make('deskripsi')
                                    ->label('Deskripsi / Spesifikasi Tambahan')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])
                            ->columns(['default' => 1, 'md' => 2]),
                    ]),
            ]);
    }
}
