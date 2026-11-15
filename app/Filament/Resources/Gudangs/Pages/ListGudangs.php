<?php

namespace App\Filament\Resources\Gudangs\Pages;

use App\Filament\Resources\Gudangs\GudangResource;
use App\Models\Gudang;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGudangs extends ListRecords
{
    protected static string $resource = GudangResource::class;

    public function getSubheading(): ?string
    {
        if (Gudang::count() >= 1) {
            return 'Sistem dikunci untuk 1 Gudang Utama. Hubungi tim pengembang jika ingin menambah gudang baru.';
        }

        return null;
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn (): bool => Gudang::count() < 1),
        ];
    }
}
