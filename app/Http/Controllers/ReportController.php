<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Procurement;
use App\Models\Product;
use App\Models\Sale;
use App\Models\ServiceJob;
use App\Models\StockMovement;
use App\Models\Tender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\View\View;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $isAdmin = ($user && $user->role && $user->role->name === 'admin');

        // PRD Section 4: Filter Domain: Semua / Tender / Jasa / Barang
        $domain = $request->get('domain', 'semua');
        if ($domain === 'all') { $domain = 'semua'; }
        $status = $request->get('status', 'semua'); // pending, approved, rejected, semua
        if ($status === 'all') { $status = 'semua'; }
        $periode = $request->get('periode', 'bulan_ini');
        $vendorRelasiFilter = $request->get('vendor_relasi', 'semua'); // semua, internal, vendor_relasi
        $search = $request->get('search');

        // Calculate Start Date and End Date based on periode preset
        [$startDate, $endDate] = $this->resolveDates($periode, $request->get('start_date'), $request->get('end_date'));

        $tenderQuery = Tender::with(['client', 'creator', 'approver'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        $serviceQuery = ServiceJob::with(['client', 'contract.client', 'creator', 'approver'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        $salesQuery = Sale::with(['creator', 'approver', 'items.product'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        // Scope to Admin's own data if Admin
        if ($isAdmin) {
            $tenderQuery->where('created_by', $user->id);
            $serviceQuery->where('created_by', $user->id);
            $salesQuery->where('created_by', $user->id);
        }

        // Apply approval status filter
        if ($status && $status !== 'semua') {
            $tenderQuery->where('approval_status', $status);
            $serviceQuery->where('approval_status', $status);
            $salesQuery->where('approval_status', $status);
        }

        // Apply vendor relasi filter on Tender
        if ($vendorRelasiFilter === 'internal') {
            $tenderQuery->where('metode_penanganan', 'internal');
        } elseif ($vendorRelasiFilter === 'vendor_relasi') {
            $tenderQuery->where('metode_penanganan', 'vendor_relasi');
        }

        // Apply search
        if ($search) {
            $tenderQuery->where(function ($q) use ($search) {
                $q->where('tender_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('nama_vendor_relasi', 'like', "%{$search}%");
            });

            $serviceQuery->where(function ($q) use ($search) {
                $q->where('job_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('klien', 'like', "%{$search}%");
            });

            $salesQuery->where(function ($q) use ($search) {
                $q->where('sale_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('nama_barang', 'like', "%{$search}%");
            });
        }

        // Summary KPI calculations
        $kpi = [
            'total_tenders' => (clone $tenderQuery)->count(),
            'tenders_value' => (clone $tenderQuery)->sum('bid_value'),
            'total_services' => (clone $serviceQuery)->count(),
            'services_value' => (clone $serviceQuery)->sum('biaya'),
            'total_sales' => (clone $salesQuery)->count(),
            'sales_value' => (clone $salesQuery)->sum('total_amount'),
            'pending_count' => (clone $tenderQuery)->where('approval_status', 'pending')->count()
                             + (clone $serviceQuery)->where('approval_status', 'pending')->count()
                             + (clone $salesQuery)->where('approval_status', 'pending')->count(),
            'approved_count' => (clone $tenderQuery)->where('approval_status', 'approved')->count()
                              + (clone $serviceQuery)->where('approval_status', 'approved')->count()
                              + (clone $salesQuery)->where('approval_status', 'approved')->count(),
            'rejected_count' => (clone $tenderQuery)->where('approval_status', 'rejected')->count()
                              + (clone $serviceQuery)->where('approval_status', 'rejected')->count()
                              + (clone $salesQuery)->where('approval_status', 'rejected')->count(),
        ];
        $kpi['total_all_transactions'] = $kpi['tenders_value'] + $kpi['services_value'] + $kpi['sales_value'];

        // Fetch data based on selected domain
        $tenders = in_array($domain, ['semua', 'tender']) ? $tenderQuery->latest()->get() : collect();
        $services = in_array($domain, ['semua', 'jasa']) ? $serviceQuery->latest()->get() : collect();
        $sales = in_array($domain, ['semua', 'barang']) ? $salesQuery->latest()->get() : collect();

        // Compatibility for legacy exports or tabs
        $type = $domain === 'semua' ? 'tender' : $domain;

        return view('reports.index', compact(
            'domain', 'status', 'periode', 'vendorRelasiFilter', 'startDate', 'endDate',
            'search', 'tenders', 'services', 'sales', 'kpi', 'isAdmin', 'type'
        ));
    }

    private function resolveDates(string $periode, ?string $start, ?string $end): array
    {
        $now = now();
        return match ($periode) {
            'hari_ini' => [$now->toDateString(), $now->toDateString()],
            'minggu_ini' => [$now->copy()->startOfWeek()->toDateString(), $now->copy()->endOfWeek()->toDateString()],
            'bulan_ini' => [$now->copy()->startOfMonth()->toDateString(), $now->copy()->endOfMonth()->toDateString()],
            'tahun_ini' => [$now->copy()->startOfYear()->toDateString(), $now->copy()->endOfYear()->toDateString()],
            'semua' => ['2020-01-01', $now->copy()->addYears(5)->toDateString()],
            'kustom' => [
                $start ?: $now->copy()->startOfMonth()->toDateString(),
                $end ?: $now->copy()->endOfMonth()->toDateString()
            ],
            default => [$now->copy()->startOfMonth()->toDateString(), $now->copy()->endOfMonth()->toDateString()],
        };
    }

    public function export(Request $request): StreamedResponse
    {
        $user = Auth::user();
        $isAdmin = ($user && $user->role && $user->role->name === 'admin');
        $domain = $request->get('domain', 'semua');
        $status = $request->get('status', 'semua');
        $periode = $request->get('periode', 'bulan_ini');
        $vendorRelasiFilter = $request->get('vendor_relasi', 'semua');

        [$startDate, $endDate] = $this->resolveDates($periode, $request->get('start_date'), $request->get('end_date'));

        $tenderQuery = Tender::with('client')->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        $serviceQuery = ServiceJob::with('client')->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        $salesQuery = Sale::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($isAdmin) {
            $tenderQuery->where('created_by', $user->id);
            $serviceQuery->where('created_by', $user->id);
            $salesQuery->where('created_by', $user->id);
        }

        if ($status !== 'semua') {
            $tenderQuery->where('approval_status', $status);
            $serviceQuery->where('approval_status', $status);
            $salesQuery->where('approval_status', $status);
        }

        if ($vendorRelasiFilter === 'internal') {
            $tenderQuery->where('metode_penanganan', 'internal');
        } elseif ($vendorRelasiFilter === 'vendor_relasi') {
            $tenderQuery->where('metode_penanganan', 'vendor_relasi');
        }

        $filename = 'laporan-spu-' . $domain . '-' . $startDate . '-sampai-' . $endDate . '.csv';

        return response()->streamDownload(function () use ($domain, $tenderQuery, $serviceQuery, $salesQuery) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['PT SIGNAL PANCA UTAMA - LAPORAN BISNIS']);
            fputcsv($handle, ['Domain', strtoupper($domain)]);
            fputcsv($handle, []);

            if (in_array($domain, ['semua', 'tender'])) {
                fputcsv($handle, ['--- DOMAIN TENDER ---']);
                fputcsv($handle, ['No. Tender', 'Nama Tender', 'Klien', 'Metode Penanganan', 'Vendor Relasi', 'Nilai Penawaran', 'Status Operasional', 'Status Approval']);
                foreach ($tenderQuery->get() as $t) {
                    fputcsv($handle, [
                        $t->tender_number, $t->name, $t->client->name ?? '-',
                        $t->metode_penanganan === 'vendor_relasi' ? 'Vendor Relasi' : 'Internal SPU',
                        $t->nama_vendor_relasi ?? '-',
                        $t->bid_value, $t->status, ucfirst($t->approval_status ?? 'approved')
                    ]);
                }
                fputcsv($handle, []);
            }

            if (in_array($domain, ['semua', 'jasa'])) {
                fputcsv($handle, ['--- DOMAIN JASA ---']);
                fputcsv($handle, ['No. Pekerjaan', 'Nama Layanan', 'Klien', 'Biaya', 'Progress', 'Status Operasional', 'Status Approval']);
                foreach ($serviceQuery->get() as $j) {
                    fputcsv($handle, [
                        $j->job_number, $j->name, $j->klien ?? ($j->client->name ?? '-'),
                        $j->biaya, $j->progress . '%', $j->status, ucfirst($j->approval_status ?? 'approved')
                    ]);
                }
                fputcsv($handle, []);
            }

            if (in_array($domain, ['semua', 'barang'])) {
                fputcsv($handle, ['--- DOMAIN BARANG ---']);
                fputcsv($handle, ['No. Penjualan', 'Pembeli / Customer', 'Nama Barang', 'Kuantitas', 'Total Nilai', 'Status Operasional', 'Status Approval']);
                foreach ($salesQuery->get() as $s) {
                    fputcsv($handle, [
                        $s->sale_number, $s->customer_name, $s->nama_barang ?? 'Produk Komputer/ATK',
                        $s->kuantitas, $s->total_amount, $s->status, ucfirst($s->approval_status ?? 'approved')
                    ]);
                }
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function exportPdf(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $dompdf = new Dompdf();
        $dompdf->loadHtml('<h2>PT Signal Panca Utama</h2><p>Laporan telah diekspor. Gunakan tombol Cetak Laporan (Print) pada antarmuka untuk format laporan PDF rapi berstandar cetak.</p>');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="laporan-spu.pdf"',
        ]);
    }

    public function exportXlsx(Request $request): StreamedResponse
    {
        return $this->export($request);
    }
}
