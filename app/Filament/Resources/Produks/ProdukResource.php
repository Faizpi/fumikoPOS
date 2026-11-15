<?php

namespace App\Filament\Resources\Produks;

use App\Filament\Resources\Produks\Pages\CreateProduk;
use App\Filament\Resources\Produks\Pages\EditProduk;
use App\Filament\Resources\Produks\Pages\ListProduks;
use App\Filament\Resources\Produks\Pages\ViewProduk;
use App\Filament\Resources\Produks\Schemas\ProdukForm;
use App\Filament\Resources\Produks\Tables\ProduksTable;
use App\Models\Produk;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class ProdukResource extends Resource
{
    protected static ?string $model = Produk::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static string|UnitEnum|null $navigationGroup = 'Gudang';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Master Barang & Ukuran';

    protected static ?string $modelLabel = 'Barang / Aset';

    protected static ?string $pluralModelLabel = 'Master Barang & Ukuran';

    protected static ?string $recordTitleAttribute = 'nama_produk';

    public static function form(Schema $schema): Schema
    {
        return ProdukForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProduksTable::configure($table);
    }

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin'], true);
    }

    public static function canCreate(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin'], true);
    }

    public static function canEdit($record): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin'], true);
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProduks::route('/'),
            'create' => CreateProduk::route('/create'),
            'view' => ViewProduk::route('/{record}'),
            'edit' => EditProduk::route('/{record}/edit'),
        ];
    }
}
