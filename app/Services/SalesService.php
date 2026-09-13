<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Exception;

class SalesService
{
    public function createSale(array $data, array $items, int $userId): Sale
    {
        return DB::transaction(function () use ($data, $items, $userId) {
            // Check stock sufficiency
            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                if ($product->stock < $item['quantity']) {
                    throw new Exception("Stok produk '{$product->name}' tidak mencukupi (Tersedia: {$product->stock}, Diminta: {$item['quantity']}).");
                }
            }

            $totalAmount = 0;
            foreach ($items as $item) {
                $totalAmount += ($item['quantity'] * $item['price']);
            }

            $sale = Sale::create([
                'sale_number' => $data['sale_number'] ?? 'SLS-' . date('Ymd') . '-' . rand(100, 999),
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'] ?? null,
                'tender_id' => $data['tender_id'] ?? null,
                'sale_date' => $data['sale_date'] ?? now()->toDateString(),
                'total_amount' => $totalAmount,
                'status' => $data['status'] ?? 'Completed',
                'notes' => $data['notes'] ?? null,
                'created_by' => $userId,
            ]);

            foreach ($items as $item) {
                $subtotal = $item['quantity'] * $item['price'];
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $subtotal,
                ]);

                // Create OUT Stock Movement
                StockMovement::create([
                    'product_id' => $item['product_id'],
                    'movement_type' => 'OUT',
                    'quantity' => $item['quantity'],
                    'reference_type' => 'Sale',
                    'reference_id' => $sale->id,
                    'movement_date' => now(),
                    'notes' => 'Penjualan barang ' . $sale->sale_number,
                    'created_by' => $userId,
                ]);
            }

            // Create Invoice automatically
            $taxAmount = $totalAmount * 0.11; // 11% PPN
            $grandTotal = $totalAmount + $taxAmount;

            Invoice::create([
                'invoice_number' => 'INV-SLS-' . date('Ymd') . '-' . rand(100, 999),
                'sale_id' => $sale->id,
                'invoice_date' => now()->toDateString(),
                'due_date' => now()->addDays(30)->toDateString(),
                'subtotal' => $totalAmount,
                'tax_amount' => $taxAmount,
                'total_amount' => $grandTotal,
                'paid_amount' => 0,
                'status' => 'Issued',
                'notes' => 'Invoice untuk Penjualan ' . $sale->sale_number,
                'created_by' => $userId,
            ]);

            return $sale;
        });
    }
}
