<x-filament-panels::page>
    @php
        $data = $this->getData();
        $stok7Hari = $this->getStok7HariData();

        $allItems = $data['gudangs']->flatMap(fn ($g) => $g->gudangProduks)->filter(fn ($s) => $s->produk !== null);
        $countBahanJual = $allItems->filter(fn ($s) => ($s->produk->kategori_barang ?? 'bahan_jual') === 'bahan_jual')->sum('stok');
        $countAset = $allItems->filter(fn ($s) => ($s->produk->kategori_barang ?? '') === 'aset_perusahaan')->sum('stok');
        $countLain = $allItems->filter(fn ($s) => ($s->produk->kategori_barang ?? '') === 'lain_lain')->sum('stok');
    @endphp

    {{-- Ringkasan Kategori Gudang Harian --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mb-4">
        <x-filament::section>
            <p class="text-sm text-gray-500 dark:text-gray-400">Bahan Dijual (Produk Styrofoam)</p>
            <p class="text-2xl font-bold text-primary-600 tabular-nums">{{ number_format($countBahanJual) }} unit</p>
        </x-filament::section>
        <x-filament::section>
            <p class="text-sm text-gray-500 dark:text-gray-400">Aset Perusahaan</p>
            <p class="text-2xl font-bold text-warning-600 tabular-nums">{{ number_format($countAset) }} unit</p>
        </x-filament::section>
        <x-filament::section>
            <p class="text-sm text-gray-500 dark:text-gray-400">Lain-lain (Pendukung Gudang)</p>
            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 tabular-nums">{{ number_format($countLain) }} unit</p>
        </x-filament::section>
    </div>

    {{-- Filter Kategori --}}
    <div class="flex flex-wrap items-center gap-2 mb-4">
        @foreach([
            'semua' => 'Semua Barang & Aset',
            'bahan_jual' => 'Bahan Dijual',
            'aset_perusahaan' => 'Aset Perusahaan',
            'lain_lain' => 'Lain-lain',
        ] as $katKey => $katLabel)
            <button
                type="button"
                wire:click="$set('filter_kategori', '{{ $katKey }}')"
                class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ ($this->filter_kategori ?? 'semua') === $katKey ? 'bg-primary-600 text-white shadow-sm' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}"
            >
                {{ $katLabel }}
            </button>
        @endforeach
    </div>

    {{-- Daftar Stok per Gudang --}}
    <div class="space-y-4">
        @forelse($data['gudangs'] as $gudang)
            @php
                $filteredProduks = $gudang->gudangProduks->filter(function ($s) {
                    if (! $s->produk) {
                        return false;
                    }
                    $kat = $this->filter_kategori ?? 'semua';
                    if ($kat === 'semua') {
                        return true;
                    }

                    return ($s->produk->kategori_barang ?? 'bahan_jual') === $kat;
                });
            @endphp
            <x-filament::section collapsible :collapsed="false">
                <x-slot name="heading">
                    <div class="flex items-center justify-between w-full">
                        <span class="font-semibold">{{ $gudang->nama_gudang }}</span>
                        <x-filament::badge color="gray">
                            {{ $filteredProduks->count() }} item |
                            Total Stok: {{ number_format($filteredProduks->sum('stok')) }}
                        </x-filament::badge>
                    </div>
                </x-slot>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm tabular-nums">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="text-left py-2 px-3 font-medium text-gray-600 dark:text-gray-400">Nama Barang</th>
                                <th class="text-left py-2 px-3 font-medium text-gray-600 dark:text-gray-400">Ukuran</th>
                                <th class="text-left py-2 px-3 font-medium text-gray-600 dark:text-gray-400">Kategori</th>
                                <th class="text-left py-2 px-3 font-medium text-gray-600 dark:text-gray-400">Kode</th>
                                <th class="text-right py-2 px-3 font-medium text-gray-600 dark:text-gray-400">Stok Jual</th>
                                <th class="text-right py-2 px-3 font-medium text-gray-600 dark:text-gray-400">Bonus/Gratis</th>
                                <th class="text-right py-2 px-3 font-medium text-gray-600 dark:text-gray-400">Sample</th>
                                <th class="text-right py-2 px-3 font-medium text-gray-600 dark:text-gray-400 font-semibold">Total Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($filteredProduks as $stok)
                                @php
                                    $kat = $stok->produk->kategori_barang ?? 'bahan_jual';
                                    $katLabel = \App\Models\Produk::KATEGORI_OPTIONS[$kat] ?? 'Bahan Dijual';
                                @endphp
                                <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <td class="py-2 px-3 font-medium">{{ $stok->produk->nama_produk }}</td>
                                    <td class="py-2 px-3 font-semibold text-primary-600 dark:text-primary-400">{{ $stok->produk->ukuran ?: '-' }}</td>
                                    <td class="py-2 px-3">
                                        <x-filament::badge color="{{ $kat === 'aset_perusahaan' ? 'warning' : ($kat === 'lain_lain' ? 'gray' : 'success') }}">
                                            {{ $katLabel }}
                                        </x-filament::badge>
                                    </td>
                                    <td class="py-2 px-3 text-gray-500 font-mono text-xs">{{ $stok->produk->item_code ?? '-' }}</td>
                                    <td class="py-2 px-3 text-right">{{ number_format($stok->stok_penjualan ?? 0) }}</td>
                                    <td class="py-2 px-3 text-right text-success-600">{{ number_format($stok->stok_gratis ?? 0) }}</td>
                                    <td class="py-2 px-3 text-right text-warning-600">{{ number_format($stok->stok_sample ?? 0) }}</td>
                                    <td class="py-2 px-3 text-right font-bold">{{ number_format($stok->stok) }} {{ $stok->produk->satuan ?? 'Pcs' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-6 text-center text-gray-500">Belum ada stok untuk kategori ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($filteredProduks->count() > 0)
                        <tfoot>
                            <tr class="bg-gray-50 dark:bg-gray-800">
                                <td colspan="4" class="py-2 px-3 font-semibold text-right">Total:</td>
                                <td class="py-2 px-3 text-right font-semibold">{{ number_format($filteredProduks->sum('stok_penjualan')) }}</td>
                                <td class="py-2 px-3 text-right font-semibold text-success-600">{{ number_format($filteredProduks->sum('stok_gratis')) }}</td>
                                <td class="py-2 px-3 text-right font-semibold text-warning-600">{{ number_format($filteredProduks->sum('stok_sample')) }}</td>
                                <td class="py-2 px-3 text-right font-bold text-primary-600">{{ number_format($filteredProduks->sum('stok')) }}</td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </x-filament::section>
        @empty
            <x-filament::section>
                <div class="text-center py-8 text-gray-500">
                    <p>Belum ada data stok tersedia.</p>
                </div>
            </x-filament::section>
        @endforelse
    </div>

    {{-- Pergerakan 7 Hari Terakhir --}}
    @if($stok7Hari->isNotEmpty())
    <x-filament::section heading="Pergerakan Stok 7 Hari Terakhir" icon="heroicon-o-clock" class="mt-4" collapsible :collapsed="true">
        <div class="overflow-x-auto">
            <table class="w-full text-sm tabular-nums">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="text-left py-2 px-3 font-medium">Barang</th>
                        <th class="text-right py-2 px-3 font-medium">Total Perubahan</th>
                        <th class="text-right py-2 px-3 font-medium">Frekuensi</th>
                        <th class="text-right py-2 px-3 font-medium">Terakhir</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stok7Hari as $log)
                    <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="py-2 px-3 font-medium">{{ $log->produk_nama }}</td>
                        <td class="py-2 px-3 text-right font-semibold">{{ number_format($log->total_perubahan) }}</td>
                        <td class="py-2 px-3 text-right">{{ $log->frekuensi }}x</td>
                        <td class="py-2 px-3 text-right text-gray-500">{{ \Carbon\Carbon::parse($log->last_change)->diffForHumans() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-filament::section>
    @endif
</x-filament-panels::page>
