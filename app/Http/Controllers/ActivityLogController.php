<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(): View
    {
        return view('activity-logs.index', [
            'logs' => $this->latestLogs(),
            'checkedAt' => now()->format('d M Y, H:i:s'),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        return response()->json([
            'logs' => $this->latestLogs(),
            'checked_at' => now()->format('d M Y, H:i:s'),
        ]);
    }

    private function latestLogs()
    {
        return AuditLog::with('user.role')
            ->latest('created_at')
            ->latest('id')
            ->limit(50)
            ->get()
            ->map(fn (AuditLog $log) => [
                'id' => $log->id,
                'user' => $log->user?->name ?? 'User tidak tersedia',
                'role' => $log->user?->role?->name ?? '-',
                'action' => $log->action,
                'resource' => $log->table_name,
                'record_id' => $log->record_id,
                'old_values' => $log->old_values,
                'new_values' => $log->new_values,
                'created_at' => $log->created_at?->toIso8601String(),
                'time' => $log->created_at?->format('d M Y, H:i:s'),
                'relative_time' => $log->created_at?->diffForHumans(now()),
            ])
            ->values();
    }
}
