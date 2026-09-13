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
        // Tender Metrics
        $tendersActive = Tender::whereNotIn('status', ['Menang', 'Kalah', 'Selesai', 'Batal'])->count();
        $tendersWon = Tender::where('result', 'Menang')->orWhere('status', 'Menang')->count();
        $tendersLost = Tender::where('result', 'Kalah')->orWhere('status', 'Kalah')->count();
        $tenderFinished = $tendersWon + $tendersLost;
        $winRate = $tenderFinished > 0 ? round(($tendersWon / $tenderFinished) * 100, 2) : 0;
        $totalTenderValue = Tender::sum('bid_value');

        // Upcoming Deadlines (within 14 days)
        $upcomingDeadlines = Tender::whereNotNull('deadline')
            ->where('deadline', '>=', now())
            ->whereNotIn('status', ['Selesai', 'Batal', 'Menang', 'Kalah'])
            ->orderBy('deadline', 'asc')
            ->take(5)
            ->get();

        // Jasa Metrics
        $activeContracts = Contract::where('status', 'Aktif')->count();
        $totalContractValue = Contract::where('status', 'Aktif')->sum('contract_value');
        $serviceRevenue = Invoice::whereNotNull('service_job_id')->sum('paid_amount');

        // Perdagangan Metrics
        $tradeRevenue = Sale::sum('total_amount');
        $totalProcurementCost = Procurement::sum('total_amount');
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

        // Top Suppliers
        $topSuppliers = Supplier::withCount('procurements')
            ->orderBy('procurements_count', 'desc')
            ->take(5)
            ->get();

        // Recent Activity
        $recentTenders = Tender::with('client')->latest()->take(5)->get();
        $recentSales = Sale::latest()->take(5)->get();

        return view('dashboard', compact(
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
            'topSuppliers',
            'recentTenders',
            'recentSales'
        ));
    }
}
