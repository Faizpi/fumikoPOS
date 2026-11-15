<?php

namespace App\Filament\Pages;

use App\Models\Gudang;
use App\Models\Pembayaran;
use App\Models\Penjualan;
use App\Services\Accounting\AccountingReportService;
use App\Services\Accounting\PiutangLedgerService;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use UnitEnum;

class PiutangPage extends Page
{
    use WithPagination;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-inbox';

    protected static string|UnitEnum|null $navigationGroup = 'Piutang';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Tagihan di Luar (Piutang)';

    protected static ?string $title = 'Tagihan Customer (Invoice di Luar)';

    protected string $view = 'filament.pages.piutang';

    public ?string $filter_from = null;

    public ?string $filter_to = null;

    public ?int $filter_gudang_id = null;

    public string $filter_status = 'semua';

    public int $perPage = 10;

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function mount(): void
    {
        $this->filter_from = now()->startOfYear()->format('Y-m-d');
        $this->filter_to = now()->addMonths(3)->format('Y-m-d');
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return in_array($user?->role, ['user', 'admin', 'spectator', 'super_admin']);
    }

    public function getChartData(): array
    {
        $user = Auth::user();

        $monthExpression = DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', tgl_jatuh_tempo)"
            : "DATE_FORMAT(tgl_jatuh_tempo, '%Y-%m')";
        $query = Penjualan::select(
            DB::raw("{$monthExpression} as bulan"),
            DB::raw('SUM(grand_total) as total'),
            DB::raw('COUNT(*) as jumlah')
        )
            ->whereNotNull('tgl_jatuh_tempo')
            ->whereIn('status', ['Approved', 'Lunas']);

        if ($user->role === 'user') {
            $query->where('user_id', $user->id);
        } elseif (in_array($user->role, ['admin', 'spectator'])) {
            if ($user->current_gudang_id) {
                $query->where('gudang_id', $user->current_gudang_id);
            }
        }

        if ($this->filter_gudang_id) {
            $query->where('gudang_id', $this->filter_gudang_id);
        }

        if ($this->filter_from) {
            $query->where('tgl_jatuh_tempo', '>=', $this->filter_from);
        }
        if ($this->filter_to) {
            $query->where('tgl_jatuh_tempo', '<=', $this->filter_to);
        }

        $rows = $query->groupBy('bulan')->orderBy('bulan')->get();

        return [
            'labels' => $rows->pluck('bulan')->map(fn ($b) => Carbon::parse($b.'-01')->format('M Y'))->toArray(),
            'totals' => $rows->pluck('total')->map(fn ($v) => (float) $v)->toArray(),
            'counts' => $rows->pluck('jumlah')->toArray(),
        ];
    }

    /** @return array<string, mixed> */
    public function getAgingSummary(): array
    {
        return app(AccountingReportService::class)->receivablePayableAging(now()->toDateString())['receivables'];
    }

    /** @return Collection<int, array<string, mixed>> */
    public function getLedgerMutationsForSale(Penjualan $sale): Collection
    {
        return app(PiutangLedgerService::class)->mutationsForSale($sale);
    }

    public function getListToko(): LengthAwarePaginator
    {
        $query = $this->basePiutangQuery();

        $paginator = $query->orderByRaw("CASE WHEN status = 'Approved' THEN 0 ELSE 1 END")
            ->orderBy('tgl_jatuh_tempo')
            ->paginate($this->perPage, ['*'], 'page', $this->getPage())
            ->onEachSide(0);

        $ledger = app(PiutangLedgerService::class);
        $paginator->setCollection($paginator->getCollection()->map(fn ($p) => $this->mapPiutangRow($p, $ledger)));

        return $paginator;
    }

    public function getListTokoAll(): Collection
    {
        $ledger = app(PiutangLedgerService::class);

        return $this->basePiutangQuery()
            ->orderByRaw("CASE WHEN status = 'Approved' THEN 0 ELSE 1 END")
            ->orderBy('tgl_jatuh_tempo')
            ->get()
            ->map(fn ($p) => $this->mapPiutangRow($p, $ledger));
    }

    private function basePiutangQuery()
    {
        $user = Auth::user();

        $query = Penjualan::with(['gudang', 'pembayarans'])
            ->whereIn('status', ['Approved', 'Lunas']);

        if ($user->role === 'user') {
            $query->where('user_id', $user->id);
        } elseif ($user->role === 'admin' && $user->current_gudang_id) {
            $query->where('gudang_id', $user->current_gudang_id);
        }

        if ($this->filter_gudang_id) {
            $query->where('gudang_id', $this->filter_gudang_id);
        }
        if ($this->filter_from) {
            $query->whereDate('tgl_transaksi', '>=', $this->filter_from);
        }
        if ($this->filter_to) {
            $query->whereDate('tgl_transaksi', '<=', $this->filter_to);
        }
        if ($this->filter_status === 'belum_lunas') {
            $query->where('status', 'Approved');
        } elseif ($this->filter_status === 'lunas') {
            $query->where('status', 'Lunas');
        }

        return $query;
    }

    private function mapPiutangRow(Penjualan $p, PiutangLedgerService $ledger): array
    {
        $mutations = $ledger->mutationsForSale($p);
        $latestMutation = $mutations->last();

        if ($mutations->isNotEmpty() && $latestMutation !== null) {
            $sisa = (float) ($latestMutation['running_balance'] ?? 0);
            $totalBayar = max(0, (float) $p->grand_total - $sisa);
        } else {
            $totalBayar = (float) Pembayaran::where('penjualan_id', $p->id)
                ->where('type', 'piutang')
                ->where('status', 'Approved')
                ->sum('jumlah_bayar');
            if ($p->status === 'Lunas' && $totalBayar <= 0) {
                $totalBayar = (float) $p->grand_total;
            }
            $sisa = max(0, (float) $p->grand_total - $totalBayar);
        }

        return [
            'id' => $p->id,
            'nomor' => $p->custom_number,
            'nama_toko' => $p->nama_toko,
            'pelanggan' => $p->pelanggan,
            'no_telepon' => $p->no_telepon,
            'syarat_pembayaran' => $p->syarat_pembayaran,
            'gudang' => $p->gudang?->nama_gudang,
            'tgl_transaksi' => $p->tgl_transaksi?->format('d/m/Y'),
            'tgl_jatuh_tempo' => $p->tgl_jatuh_tempo?->format('d/m/Y'),
            'jatuh_tempo_lewat' => $p->tgl_jatuh_tempo?->isPast() && $p->status === 'Approved',
            'grand_total' => (float) $p->grand_total,
            'sudah_bayar' => $totalBayar,
            'sisa' => $sisa,
            'debit' => $latestMutation['debit'] ?? '0.00',
            'credit' => $latestMutation['credit'] ?? '0.00',
            'saldo' => $latestMutation['running_balance'] ?? number_format($sisa, 2, '.', ''),
            'journal_number' => $latestMutation['journal_number'] ?? null,
            'status' => $p->status,
        ];
    }

    protected function getHeaderActions(): array
    {
        $user = Auth::user();

        return [
            Action::make('filter')
                ->label('Filter Periode')
                ->icon('heroicon-o-funnel')
                ->color('gray')
                ->form([
                    DatePicker::make('from')->label('Dari Tanggal')->default($this->filter_from)->required(),
                    DatePicker::make('to')->label('Sampai Tanggal')->default($this->filter_to)->required(),
                    Select::make('gudang_id')
                        ->label('Gudang (Opsional)')
                        ->options(fn () => Gudang::pluck('nama_gudang', 'id'))
                        ->placeholder('Semua Gudang')
                        ->searchable()->preload(),
                ])
                ->action(function (array $data) {
                    $this->filter_from = $data['from'];
                    $this->filter_to = $data['to'];
                    $this->filter_gudang_id = $data['gudang_id'] ?? null;
                    $this->resetPage();
                })
                ->modalSubmitActionLabel('Terapkan')
                ->modalCancelActionLabel('Batal'),

            Action::make('exportPdf')
                ->label('Export PDF Harian')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->visible(fn () => $user?->canExportPdf())
                ->action(function () use ($user) {
                    $pdf = app('dompdf.wrapper');
                    $pdf->loadView('reports.piutang-pdf', [
                        'list' => $this->getListTokoAll(),
                        'from' => $this->filter_from,
                        'to' => $this->filter_to,
                        'generatedBy' => $user->name,
                    ]);
                    $filename = 'Piutang_'.now()->format('Ymd').'.pdf';

                    return response()->streamDownload(fn () => print ($pdf->output()), $filename);
                }),
        ];
    }
}
