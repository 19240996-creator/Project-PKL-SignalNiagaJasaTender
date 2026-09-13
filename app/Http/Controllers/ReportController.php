<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Procurement;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\Tender;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->get('type', 'tender');
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $data = [];

        switch ($type) {
            case 'tender':
                $data = Tender::with('client')
                    ->whereBetween('found_date', [$startDate, $endDate])
                    ->get();
                break;

            case 'contract':
                $data = Contract::with('client')
                    ->whereBetween('start_date', [$startDate, $endDate])
                    ->get();
                break;

            case 'procurement':
                $data = Procurement::with(['supplier', 'items.product'])
                    ->whereBetween('procurement_date', [$startDate, $endDate])
                    ->get();
                break;

            case 'sales':
                $data = Sale::with(['items.product'])
                    ->whereBetween('sale_date', [$startDate, $endDate])
                    ->get();
                break;

            case 'stock':
                $data = Product::with('stockMovements')->get();
                break;

            case 'invoice':
                $data = Invoice::with(['payments'])
                    ->whereBetween('invoice_date', [$startDate, $endDate])
                    ->get();
                break;

            case 'payment':
                $data = Payment::with(['invoice'])
                    ->whereBetween('payment_date', [$startDate, $endDate])
                    ->get();
                break;
        }

        return view('reports.index', compact('type', 'startDate', 'endDate', 'data'));
    }
}
