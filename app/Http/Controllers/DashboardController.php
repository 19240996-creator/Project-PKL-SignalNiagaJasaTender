<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Procurement;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\Tender;
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
            $startDate = \Carbon\Carbon::parse($request->start_date)->startOfDay();
            $endDate = \Carbon\Carbon::parse($request->end_date)->endOfDay();
        }

        // Tender Metrics
        $tenderBase = Tender::query();
        if ($startDate && $endDate) {
            $tenderBase->whereBetween('found_date', [$startDate->toDateString(), $endDate->toDateString()]);
        }

        $tendersActive = (clone $tenderBase)->whereNotIn('status', ['Menang', 'Kalah', 'Selesai', 'Batal'])->count();
        $tendersWon = (clone $tenderBase)->where(function ($q) {
            $q->where('result', 'Menang')->orWhere('status', 'Menang');
        })->count();
        $tendersLost = (clone $tenderBase)->where(function ($q) {
            $q->where('result', 'Kalah')->orWhere('status', 'Kalah');
        })->count();
        $tenderFinished = $tendersWon + $tendersLost;
        $winRate = $tenderFinished > 0 ? round(($tendersWon / $tenderFinished) * 100, 2) : 0;
        $totalTenderValue = (clone $tenderBase)->sum('bid_value');

        // Upcoming Deadlines (within 14 days)
        $upcomingDeadlines = Tender::whereNotNull('deadline')
            ->where('deadline', '>=', now())
            ->where('deadline', '<=', now()->addDays(14))
            ->whereNotIn('status', ['Selesai', 'Batal', 'Menang', 'Kalah'])
            ->orderBy('deadline', 'asc')
            ->take(5)
            ->get();

        // Jasa Metrics
        $contractBase = Contract::query();
        if ($startDate && $endDate) {
            $contractBase->whereBetween('start_date', [$startDate->toDateString(), $endDate->toDateString()]);
        }
        $activeContracts = (clone $contractBase)->where('status', 'Aktif')->count();
        $totalContractValue = (clone $contractBase)->where('status', 'Aktif')->sum('contract_value');

        $invoiceBase = Invoice::query();
        if ($startDate && $endDate) {
            $invoiceBase->whereBetween('invoice_date', [$startDate->toDateString(), $endDate->toDateString()]);
        }
        $serviceRevenue = (clone $invoiceBase)->whereNotNull('service_job_id')->sum('paid_amount');

        // Perdagangan Metrics
        $saleBase = Sale::query();
        if ($startDate && $endDate) {
            $saleBase->whereBetween('sale_date', [$startDate->toDateString(), $endDate->toDateString()]);
        }
        $tradeRevenue = (clone $saleBase)->sum('total_amount');

        $procurementBase = Procurement::query();
        if ($startDate && $endDate) {
            $procurementBase->whereBetween('procurement_date', [$startDate->toDateString(), $endDate->toDateString()]);
        }
        $totalProcurementCost = (clone $procurementBase)->sum('total_amount');

        $totalOutstandingPiutang = Invoice::whereIn('status', ['Issued', 'Partial', 'Overdue'])
            ->selectRaw('SUM(total_amount - paid_amount) as remaining')
            ->value('remaining') ?? 0;

        $unpaidInvoicesCount = Invoice::whereIn('status', ['Issued', 'Partial', 'Overdue'])->count();
        $totalPaidAmount = Invoice::sum('paid_amount');

        // Products & Stock
        $products = Product::all();
        $lowStockProducts = $products->filter(function ($product) {
            return $product->isLowStock();
        });

        // Top Selling Products (PRD Seksi 9 & Seksi 22)
        $topSellingProducts = Product::withSum('salesItems as total_sold', 'quantity')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        // Top Tender Items (PRD Seksi 22 poin 9)
        $topTenderItems = Product::withSum('tenderItems as total_tender_needed', 'quantity')
            ->orderByDesc('total_tender_needed')
            ->take(5)
            ->get();

        // Top Suppliers
        $topSuppliers = Supplier::withCount('procurements')
            ->orderBy('procurements_count', 'desc')
            ->take(5)
            ->get();

        // Recent Activity
        $recentTenders = Tender::with('client')->latest()->take(5)->get();
        $recentSales = Sale::latest()->take(5)->get();
        $notifications = $request->user()->unreadNotifications()->latest()->take(5)->get();

        return view('dashboard', compact(
            'period',
            'startDate',
            'endDate',
            'tendersActive',
            'tendersWon',
            'tendersLost',
            'winRate',
            'totalTenderValue',
            'upcomingDeadlines',
            'activeContracts',
            'totalContractValue',
            'serviceRevenue',
            'tradeRevenue',
            'totalProcurementCost',
            'totalOutstandingPiutang',
            'unpaidInvoicesCount',
            'totalPaidAmount',
            'lowStockProducts',
            'topSellingProducts',
            'topTenderItems',
            'topSuppliers',
            'recentTenders',
            'recentSales',
            'notifications'
        ));
    }
}
