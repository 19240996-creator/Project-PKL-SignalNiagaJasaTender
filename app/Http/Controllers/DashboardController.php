<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Procurement;
use App\Models\Product;
use App\Models\Sale;
use App\Models\ServiceJob;
use App\Models\Supplier;
use App\Models\Tender;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $period = $request->get('period', 'all');
        $startDate = null;
        $endDate = null;

        if ($period === 'today') {
            $startDate = now()->startOfDay();
            $endDate = now()->endOfDay();
        } elseif ($period === 'week') {
            $startDate = now()->startOfWeek();
            $endDate = now()->endOfWeek();
        } elseif ($period === 'month') {
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();
        } elseif ($period === 'year') {
            $startDate = now()->startOfYear();
            $endDate = now()->endOfYear();
        } elseif ($period === 'custom' && $request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
        }

        // ==========================================
        // 11.1 RINGKASAN TENDER
        // ==========================================
        $tenderBase = Tender::query();
        if ($startDate && $endDate) {
            $tenderBase->whereBetween('found_date', [$startDate->toDateString(), $endDate->toDateString()]);
        }

        $totalTenders = (clone $tenderBase)->count();
        $tendersActive = (clone $tenderBase)->whereNotIn('status', ['Menang', 'Kalah', 'Selesai', 'Batal'])->count();
        $tendersWon = (clone $tenderBase)->where(function ($q) {
            $q->where('result', 'Menang')->orWhere('status', 'Menang');
        })->count();
        $tendersLost = (clone $tenderBase)->where(function ($q) {
            $q->where('result', 'Kalah')->orWhere('status', 'Kalah');
        })->count();
        $tenderFinished = $tendersWon + $tendersLost;
        $winRate = $tenderFinished > 0 ? round(($tendersWon / $tenderFinished) * 100, 1) : 0;
        $totalTenderValue = (clone $tenderBase)->sum('bid_value');

        // Upcoming Deadlines (within 14 days)
        $upcomingDeadlines = Tender::with('client')
            ->whereNotNull('deadline')
            ->where('deadline', '>=', now())
            ->where('deadline', '<=', now()->addDays(14))
            ->whereNotIn('status', ['Selesai', 'Batal', 'Menang', 'Kalah'])
            ->orderBy('deadline', 'asc')
            ->take(5)
            ->get();

        // ==========================================
        // 11.2 RINGKASAN JASA
        // ==========================================
        $contractBase = Contract::query();
        if ($startDate && $endDate) {
            $contractBase->whereBetween('start_date', [$startDate->toDateString(), $endDate->toDateString()]);
        }
        $activeContracts = (clone $contractBase)->where('status', 'Aktif')->count();
        $totalContractValue = (clone $contractBase)->where('status', 'Aktif')->sum('contract_value');
        $allContractsValue = (clone $contractBase)->sum('contract_value');
        $totalServiceValue = $totalContractValue > 0 ? $totalContractValue : $allContractsValue;

        $serviceJobBase = ServiceJob::query();
        if ($startDate && $endDate) {
            $serviceJobBase->whereBetween('start_date', [$startDate->toDateString(), $endDate->toDateString()]);
        }
        $totalServices = (clone $serviceJobBase)->count();
        $servicesActive = (clone $serviceJobBase)->whereNotIn('status', ['Selesai', 'Batal'])->count();
        $servicesCompleted = (clone $serviceJobBase)->where('status', 'Selesai')->count();

        $invoiceBase = Invoice::query();
        if ($startDate && $endDate) {
            $invoiceBase->whereBetween('invoice_date', [$startDate->toDateString(), $endDate->toDateString()]);
        }
        $totalServiceBilled = (clone $invoiceBase)->whereNotNull('service_job_id')->where('status', '!=', 'Cancelled')->sum('total_amount');
        $serviceRevenue = (clone $invoiceBase)->whereNotNull('service_job_id')->sum('paid_amount');

        // ==========================================
        // 11.3 RINGKASAN BARANG
        // ==========================================
        $procurementBase = Procurement::query();
        if ($startDate && $endDate) {
            $procurementBase->whereBetween('procurement_date', [$startDate->toDateString(), $endDate->toDateString()]);
        }
        $totalProcurements = (clone $procurementBase)->count();
        $totalProcurementCost = (clone $procurementBase)->sum('total_amount');
        $procurementsActive = (clone $procurementBase)->whereIn('status', ['Draft', 'Submitted', 'Pending', 'Diproses', 'Ordered'])->count();

        $saleBase = Sale::query();
        if ($startDate && $endDate) {
            $saleBase->whereBetween('sale_date', [$startDate->toDateString(), $endDate->toDateString()]);
        }
        $totalSales = (clone $saleBase)->count();
        $tradeRevenue = (clone $saleBase)->sum('total_amount'); // Nilai Penjualan

        $totalGoodsTransactions = $totalProcurements + $totalSales;

        // Stock & Products
        $products = Product::all();
        $totalStockUnits = (int) $products->sum('stock');
        $totalProductItems = $products->count();
        $lowStockProducts = $products->filter(fn ($p) => $p->isLowStock());

        // Receivables & Invoices
        $totalOutstandingPiutang = (clone $invoiceBase)->whereIn('status', ['Issued', 'Partial', 'Overdue'])
            ->selectRaw('SUM(total_amount - paid_amount) as remaining')
            ->value('remaining') ?? 0;
        $unpaidInvoicesCount = Invoice::whereIn('status', ['Issued', 'Partial', 'Overdue'])->count();
        $totalPaidAmount = Invoice::sum('paid_amount');

        // ==========================================
        // 11.4 RINGKASAN PERUSAHAAN
        // ==========================================
        $companyTotalActivities = $totalTenders + $totalServices + $totalGoodsTransactions;
        $companyTotalTransactionValue = (float) ($totalTenderValue + $totalServiceValue + $tradeRevenue + $totalProcurementCost);
        $companyTotalRevenue = (float) ($serviceRevenue + $tradeRevenue);
        $companyActiveActivities = $tendersActive + $servicesActive + $procurementsActive;

        // Aktivitas yang membutuhkan persetujuan sesuai PRD (approval_status = 'pending')
        $pendingTendersCount = Tender::where('approval_status', 'pending')->count();
        $pendingServicesCount = ServiceJob::where('approval_status', 'pending')->count();
        $pendingSalesCount = Sale::where('approval_status', 'pending')->count();
        $totalPendingApprovals = $pendingTendersCount + $pendingServicesCount + $pendingSalesCount;
        $companyPendingApproval = $totalPendingApprovals;

        // Top Items (PRD Seksi 9 & 22)
        $topSellingProducts = Product::withSum('salesItems as total_sold', 'quantity')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        $topTenderItems = Product::withSum('tenderItems as total_tender_needed', 'quantity')
            ->orderByDesc('total_tender_needed')
            ->take(5)
            ->get();

        $topSuppliers = Supplier::withCount('procurements')
            ->orderBy('procurements_count', 'desc')
            ->take(5)
            ->get();

        // Recent Activities across 3 Domains
        $recentTenders = Tender::with('client')->latest()->take(5)->get();
        $recentServiceJobs = ServiceJob::with('contract.client')->latest()->take(5)->get();
        $recentSales = Sale::latest()->take(5)->get();
        $recentProcurements = Procurement::with('supplier')->latest()->take(5)->get();
        $notifications = $request->user()?->unreadNotifications()->latest()->take(5)->get() ?? collect();

        return view('dashboard', compact(
            'period',
            'startDate',
            'endDate',
            // 11.4 Ringkasan Perusahaan & PRD Approval
            'companyTotalActivities',
            'companyTotalTransactionValue',
            'companyTotalRevenue',
            'companyActiveActivities',
            'companyPendingApproval',
            'pendingTendersCount',
            'pendingServicesCount',
            'pendingSalesCount',
            'totalPendingApprovals',
            // 11.1 Ringkasan Tender
            'totalTenders',
            'tendersActive',
            'tendersWon',
            'tendersLost',
            'totalTenderValue',
            'winRate',
            'upcomingDeadlines',
            // 11.2 Ringkasan Jasa
            'totalServices',
            'servicesActive',
            'servicesCompleted',
            'totalServiceValue',
            'totalServiceBilled',
            'serviceRevenue',
            'activeContracts',
            'totalContractValue',
            // 11.3 Ringkasan Barang
            'totalGoodsTransactions',
            'totalProcurements',
            'totalProcurementCost',
            'totalSales',
            'tradeRevenue',
            'totalStockUnits',
            'totalProductItems',
            'lowStockProducts',
            // Additional metrics & lists
            'totalOutstandingPiutang',
            'unpaidInvoicesCount',
            'totalPaidAmount',
            'topSellingProducts',
            'topTenderItems',
            'topSuppliers',
            'recentTenders',
            'recentServiceJobs',
            'recentSales',
            'recentProcurements',
            'notifications'
        ));
    }
}
