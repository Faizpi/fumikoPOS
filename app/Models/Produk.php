<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $fillable = [
        'nama_produk',
        'ukuran',
        'kategori_barang',
        'item_code',
        'jumlah_item',
        'harga',
        'harga_grosir',
        'satuan',
        'deskripsi',
    ];

    public const KATEGORI_OPTIONS = [
        'bahan_jual' => 'Bahan Dijual (Produk)',
        'aset_perusahaan' => 'Aset Perusahaan',
        'lain_lain' => 'Lain-lain (Pendukung)',
    ];

    public function getKategoriLabelAttribute(): string
    {
        return self::KATEGORI_OPTIONS[$this->kategori_barang ?? 'bahan_jual'] ?? 'Bahan Dijual (Produk)';
    }

    public function getNamaDenganUkuranAttribute(): string
    {
        if (! empty($this->ukuran)) {
            return "{$this->nama_produk} ({$this->ukuran})";
        }

        return (string) $this->nama_produk;
    }

    protected function casts(): array
    {
        return [
            'harga' => 'decimal:2',
            'harga_grosir' => 'decimal:2',
        ];
    }

    public function stokDiGudang()
    {
        return $this->hasMany(GudangProduk::class);
    }
}
