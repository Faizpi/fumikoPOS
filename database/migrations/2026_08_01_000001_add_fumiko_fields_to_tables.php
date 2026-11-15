<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('produks')) {
            Schema::table('produks', function (Blueprint $table) {
                if (! Schema::hasColumn('produks', 'ukuran')) {
                    $table->string('ukuran', 100)->nullable()->after('nama_produk');
                }
                if (! Schema::hasColumn('produks', 'kategori_barang')) {
                    $table->string('kategori_barang', 50)->default('bahan_jual')->after('ukuran');
                }
            });
        }

        if (Schema::hasTable('kontaks')) {
            Schema::table('kontaks', function (Blueprint $table) {
                if (! Schema::hasColumn('kontaks', 'nama_toko')) {
                    $table->string('nama_toko', 255)->nullable()->after('nama');
                }
                if (! Schema::hasColumn('kontaks', 'tipe_kontak')) {
                    $table->string('tipe_kontak', 50)->default('customer')->after('nama_toko');
                }
            });
        }

        if (Schema::hasTable('penjualans')) {
            Schema::table('penjualans', function (Blueprint $table) {
                if (! Schema::hasColumn('penjualans', 'nama_toko')) {
                    $table->string('nama_toko', 255)->nullable()->after('pelanggan');
                }
            });
        }

        if (Schema::hasTable('penjualan_items')) {
            Schema::table('penjualan_items', function (Blueprint $table) {
                if (! Schema::hasColumn('penjualan_items', 'ukuran')) {
                    $table->string('ukuran', 100)->nullable()->after('produk_id');
                }
            });
        }

        if (Schema::hasTable('pembelians')) {
            Schema::table('pembelians', function (Blueprint $table) {
                if (! Schema::hasColumn('pembelians', 'no_surat_jalan')) {
                    $table->string('no_surat_jalan', 255)->nullable()->after('no_referensi');
                }
            });
        }

        if (Schema::hasTable('pembelian_items')) {
            Schema::table('pembelian_items', function (Blueprint $table) {
                if (! Schema::hasColumn('pembelian_items', 'ukuran')) {
                    $table->string('ukuran', 100)->nullable()->after('produk_id');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('produks')) {
            Schema::table('produks', function (Blueprint $table) {
                $cols = array_filter(['ukuran', 'kategori_barang'], fn ($c) => Schema::hasColumn('produks', $c));
                if ($cols) {
                    $table->dropColumn($cols);
                }
            });
        }

        if (Schema::hasTable('kontaks')) {
            Schema::table('kontaks', function (Blueprint $table) {
                $cols = array_filter(['nama_toko', 'tipe_kontak'], fn ($c) => Schema::hasColumn('kontaks', $c));
                if ($cols) {
                    $table->dropColumn($cols);
                }
            });
        }

        if (Schema::hasTable('penjualans') && Schema::hasColumn('penjualans', 'nama_toko')) {
            Schema::table('penjualans', function (Blueprint $table) {
                $table->dropColumn('nama_toko');
            });
        }

        if (Schema::hasTable('penjualan_items') && Schema::hasColumn('penjualan_items', 'ukuran')) {
            Schema::table('penjualan_items', function (Blueprint $table) {
                $table->dropColumn('ukuran');
            });
        }

        if (Schema::hasTable('pembelians') && Schema::hasColumn('pembelians', 'no_surat_jalan')) {
            Schema::table('pembelians', function (Blueprint $table) {
                $table->dropColumn('no_surat_jalan');
            });
        }

        if (Schema::hasTable('pembelian_items') && Schema::hasColumn('pembelian_items', 'ukuran')) {
            Schema::table('pembelian_items', function (Blueprint $table) {
                $table->dropColumn('ukuran');
            });
        }
    }
};
