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
use Symfony\Component\HttpFoundation\StreamedResponse;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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

    public function export(Request $request): StreamedResponse
    {
        $type = $request->get('type', 'tender');
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $rows = match ($type) {
            'contract' => Contract::with('client')->whereBetween('start_date', [$startDate, $endDate])->get(),
            'procurement' => Procurement::with('supplier')->whereBetween('procurement_date', [$startDate, $endDate])->get(),
            'sales' => Sale::whereBetween('sale_date', [$startDate, $endDate])->get(),
            'stock' => Product::with('stockMovements')->get(),
            'invoice' => Invoice::whereBetween('invoice_date', [$startDate, $endDate])->get(),
            'payment' => Payment::whereBetween('payment_date', [$startDate, $endDate])->get(),
            default => Tender::with('client')->whereBetween('found_date', [$startDate, $endDate])->get(),
        };

        $filename = 'laporan-' . $type . '-' . $startDate . '-sampai-' . $endDate . '.csv';

        return response()->streamDownload(function () use ($rows, $type) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Laporan', ucfirst($type)]);
            fputcsv($handle, []);

            foreach ($rows as $row) {
                fputcsv($handle, match ($type) {
                    'contract' => [$row->contract_number, $row->client->name ?? '-', $row->start_date, $row->end_date, $row->contract_value, $row->status],
                    'procurement' => [$row->procurement_number, $row->supplier->name ?? '-', $row->procurement_date, $row->total_amount, $row->status],
                    'sales' => [$row->sale_number, $row->customer_name, $row->sale_date, $row->total_amount, $row->status],
                    'stock' => [$row->sku, $row->name, $row->stock, $row->minimum_stock],
                    'invoice' => [$row->invoice_number, $row->invoice_date, $row->due_date, $row->total_amount, $row->paid_amount, $row->status],
                    'payment' => [$row->invoice->invoice_number ?? '-', $row->payment_date, $row->amount, $row->payment_method],
                    default => [$row->tender_number, $row->name, $row->client->name ?? '-', $row->deadline, $row->bid_value, $row->status],
                });
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function exportPdf(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $type = $request->get('type', 'tender');
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());
        $data = $this->reportRows($type, $startDate, $endDate);
        $html = view('reports.pdf', compact('type', 'startDate', 'endDate', 'data'))->render();
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        return response($dompdf->output(), 200, ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'attachment; filename="laporan-' . $type . '.pdf"']);
    }

    public function exportXlsx(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $type = $request->get('type', 'tender');
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());
        $rows = $this->reportRows($type, $startDate, $endDate);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(['Laporan', ucfirst($type), 'Periode', "$startDate - $endDate"], null, 'A1');
        $sheet->fromArray($this->exportHeaders($type), null, 'A3');
        $sheet->fromArray($this->exportValues($type, $rows), null, 'A4');
        $writer = new Xlsx($spreadsheet);
        ob_start(); $writer->save('php://output'); $content = ob_get_clean();
        return response($content, 200, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'Content-Disposition' => 'attachment; filename="laporan-' . $type . '.xlsx"']);
    }

    private function reportRows(string $type, string $startDate, string $endDate)
    {
        return match ($type) {
            'contract' => Contract::with('client')->whereBetween('start_date', [$startDate, $endDate])->get(),
            'procurement' => Procurement::with('supplier')->whereBetween('procurement_date', [$startDate, $endDate])->get(),
            'sales' => Sale::whereBetween('sale_date', [$startDate, $endDate])->get(), 'stock' => Product::all(),
            'invoice' => Invoice::whereBetween('invoice_date', [$startDate, $endDate])->get(), 'payment' => Payment::with('invoice')->whereBetween('payment_date', [$startDate, $endDate])->get(),
            default => Tender::with('client')->whereBetween('found_date', [$startDate, $endDate])->get(),
        };
    }

    public function exportHeaders(string $type): array { return match ($type) {
        'contract' => ['Nomor', 'Klien', 'Mulai', 'Selesai', 'Nilai', 'Status'], 'procurement' => ['Nomor', 'Supplier', 'Tanggal', 'Total', 'Status'], 'sales' => ['Nomor', 'Pelanggan', 'Tanggal', 'Total', 'Status'], 'stock' => ['SKU', 'Produk', 'Stok', 'Minimum'], 'invoice' => ['Nomor', 'Tanggal', 'Jatuh Tempo', 'Total', 'Terbayar', 'Status'], 'payment' => ['Invoice', 'Tanggal', 'Jumlah', 'Metode'], default => ['Nomor', 'Nama', 'Klien', 'Deadline', 'Penawaran', 'Status'],
    }; }

    public function exportValues(string $type, $rows): array { return $rows->map(fn ($row) => match ($type) {
        'contract' => [$row->contract_number, $row->client->name ?? '-', $row->start_date, $row->end_date, $row->contract_value, $row->status], 'procurement' => [$row->procurement_number, $row->supplier->name ?? '-', $row->procurement_date, $row->total_amount, $row->status], 'sales' => [$row->sale_number, $row->customer_name, $row->sale_date, $row->total_amount, $row->status], 'stock' => [$row->sku, $row->name, $row->stock, $row->minimum_stock], 'invoice' => [$row->invoice_number, $row->invoice_date, $row->due_date, $row->total_amount, $row->paid_amount, $row->status], 'payment' => [$row->invoice->invoice_number ?? '-', $row->payment_date, $row->amount, $row->payment_method], default => [$row->tender_number, $row->name, $row->client->name ?? '-', $row->deadline, $row->bid_value, $row->status],
    })->all(); }
}
