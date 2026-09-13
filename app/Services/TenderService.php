<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Tender;
use Illuminate\Support\Facades\DB;
use Exception;

class TenderService
{
    /**
     * Convert a won tender to a contract
     */
    public function convertTenderToContract(Tender $tender, array $contractData, int $userId): Contract
    {
        if ($tender->result !== 'Menang' && $tender->status !== 'Menang') {
            throw new Exception('Tender harus berstatus Menang untuk dikonversi menjadi kontrak.');
        }

        return DB::transaction(function () use ($tender, $contractData, $userId) {
            $existing = Contract::where('tender_id', $tender->id)->first();
            if ($existing) {
                return $existing;
            }

            $contractValue = $contractData['contract_value'] ?? $tender->bid_value;
            $feePercentage = $contractData['fee_percentage'] ?? 0;
            $feeAmount = ($contractValue * $feePercentage) / 100;

            $contract = Contract::create([
                'tender_id' => $tender->id,
                'client_id' => $tender->client_id,
                'contract_number' => $contractData['contract_number'] ?? 'CTR-' . strtoupper(uniqid()),
                'start_date' => $contractData['start_date'] ?? now()->toDateString(),
                'end_date' => $contractData['end_date'] ?? now()->addYear()->toDateString(),
                'contract_value' => $contractValue,
                'fee_percentage' => $feePercentage,
                'fee_amount' => $feeAmount,
                'status' => 'Aktif',
                'notes' => 'Dikonversi dari Tender: ' . $tender->tender_number,
                'created_by' => $userId,
            ]);

            $tender->update([
                'status' => 'Kontrak',
                'result' => 'Menang',
            ]);

            return $contract;
        });
    }
}
