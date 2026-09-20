<?php

namespace App\Services;

use App\Models\Procurement;
use App\Models\ProcurementItem;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class ProcurementService
{
    public function createProcurement(array $data, array $items, int $userId): Procurement
    {
        return DB::transaction(function () use ($data, $items, $userId) {
            $totalAmount = 0;
            foreach ($items as $item) {
                $totalAmount += ($item['quantity'] * $item['price']);
            }

            $procurement = Procurement::create([
                'procurement_number' => $data['procurement_number'] ?? 'PRC-' . date('Ymd') . '-' . rand(100, 999),
                'supplier_id' => $data['supplier_id'],
                'tender_id' => $data['tender_id'] ?? null,
                'procurement_date' => $data['procurement_date'] ?? now()->toDateString(),
                'total_amount' => $totalAmount,
                'status' => $data['status'] ?? 'Received',
                'notes' => $data['notes'] ?? null,
                'created_by' => $userId,
            ]);

            foreach ($items as $item) {
                $subtotal = $item['quantity'] * $item['price'];
                ProcurementItem::create([
                    'procurement_id' => $procurement->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $subtotal,
                ]);

                if (($data['status'] ?? 'Received') === 'Received') {
                    StockMovement::create([
                        'product_id' => $item['product_id'],
                        'movement_type' => 'IN',
                        'quantity' => $item['quantity'],
                        'reference_type' => 'Procurement',
                        'reference_id' => $procurement->id,
                        'movement_date' => now(),
                        'notes' => 'Pengadaan barang ' . $procurement->procurement_number,
                        'created_by' => $userId,
                    ]);
                }
            }

            return $procurement;
        });
    }

    public function receiveProcurement(Procurement $procurement, int $userId): Procurement
    {
        return DB::transaction(function () use ($procurement, $userId) {
            if ($procurement->status === 'Received') {
                return $procurement;
            }

            foreach ($procurement->items as $item) {
                StockMovement::create([
                    'product_id' => $item->product_id,
                    'movement_type' => 'IN',
                    'quantity' => $item->quantity,
                    'reference_type' => 'Procurement',
                    'reference_id' => $procurement->id,
                    'movement_date' => now(),
                    'notes' => 'Penerimaan pengadaan ' . $procurement->procurement_number,
                    'created_by' => $userId,
                ]);
            }

            $procurement->update(['status' => 'Received']);

            return $procurement->fresh();
        });
    }
}
