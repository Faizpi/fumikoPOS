<?php

namespace App\Filament\Resources\Kontaks\Schemas;

use App\Models\Kontak;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class KontakForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 1, 'md' => 2])
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Informasi Kontak')
                            ->description('Data pembeli (toko/customer) atau supplier untuk transaksi.')
                            ->icon('heroicon-o-user-circle')
                            ->schema([
                                TextInput::make('kode_kontak')
                                    ->label('Kode Kontak')
                                    ->maxLength(50)
                                    ->placeholder(fn () => 'Contoh: '.Kontak::generateKodeKontak())
                                    ->helperText('Kosongkan untuk auto-generate')
                                    ->disabled(fn (string $operation) => $operation === 'edit' && ! in_array(auth()->user()?->role, ['admin', 'super_admin'])),

                                Select::make('tipe_kontak')
                                    ->label('Tipe Kontak')
                                    ->options([
                                        'customer' => 'Customer / Pembeli',
                                        'supplier' => 'Supplier / Pemasok',
                                        'keduanya' => 'Customer & Supplier',
                                    ])
                                    ->default('customer')
                                    ->required()
                                    ->native(false),

                                TextInput::make('nama')
                                    ->label('Nama Pembeli / Kontak')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Contoh: Bpk. Hendra Wijaya')
                                    ->disabled(fn (string $operation) => $operation === 'edit' && ! in_array(auth()->user()?->role, ['admin', 'super_admin'])),

                                TextInput::make('nama_toko')
                                    ->label('Nama Toko / Perusahaan')
                                    ->maxLength(255)
                                    ->placeholder('Contoh: Toko Plastik & Kemasan Makmur')
                                    ->disabled(fn (string $operation) => $operation === 'edit' && ! in_array(auth()->user()?->role, ['admin', 'super_admin'])),

                                TextInput::make('no_telp')
                                    ->label('No. HP / WhatsApp')
                                    ->tel()
                                    ->maxLength(20)
                                    ->placeholder('08xxxxxxxxxx / 628xxxxxxxxxx')
                                    ->helperText('Nomor HP aktif untuk konfirmasi pesanan & surat jalan'),

                                TextInput::make('email')
                                    ->label('Email (Opsional)')
                                    ->email()
                                    ->maxLength(255)
                                    ->disabled(fn (string $operation) => $operation === 'edit' && ! in_array(auth()->user()?->role, ['admin', 'super_admin'])),
                            ])
                            ->columns(['default' => 1, 'md' => 2]),

                        Section::make('Alamat & Detail Tambahan')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                Textarea::make('alamat')
                                    ->label('Alamat Lengkap Toko / Pengiriman')
                                    ->rows(3)
                                    ->placeholder('Jl. Raya Industri No. 12, Blok B, Kota...')
                                    ->columnSpanFull()
                                    ->disabled(fn (string $operation) => $operation === 'edit' && ! in_array(auth()->user()?->role, ['admin', 'super_admin'])),

                                TextInput::make('diskon_persen')
                                    ->label('Diskon Bawaan (%)')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->suffix('%')
                                    ->helperText('Diskon otomatis untuk customer ini')
                                    ->disabled(fn () => ! in_array(auth()->user()?->role, ['admin', 'super_admin'])),

                                Select::make('gudang_id')
                                    ->label('Gudang')
                                    ->relationship('gudang', 'nama_gudang')
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Pilih gudang')
                                    ->default(fn () => auth()->user()?->getCurrentGudang()?->id)
                                    ->visible(fn () => auth()->user()?->isSuperAdmin())
                                    ->disabled(fn () => ! auth()->user()?->isSuperAdmin()),
                            ])
                            ->columns(['default' => 1, 'md' => 2]),
                    ]),
            ]);
    }
}
