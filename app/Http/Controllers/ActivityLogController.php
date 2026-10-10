<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $paginatedLogs = $this->latestLogs($request);

        return view('activity-logs.index', [
            'logs' => $paginatedLogs->items(),
            'pagination' => $this->paginationMeta($paginatedLogs),
            'checkedAt' => now()->format('d M Y, H:i:s'),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $paginatedLogs = $this->latestLogs($request);

        return response()->json([
            'logs' => $paginatedLogs->items(),
            'pagination' => $this->paginationMeta($paginatedLogs),
            'checked_at' => now()->format('d M Y, H:i:s'),
        ]);
    }

    private function latestLogs(Request $request)
    {
        return AuditLog::with('user.role')
            ->when(
                in_array($request->input('action'), ['create', 'update', 'delete'], true),
                fn ($query) => $query->where('action', $request->input('action'))
            )
            ->latest('created_at')
            ->latest('id')
            ->paginate(15)
            ->through(fn (AuditLog $log) => [
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
            ]);
    }

    private function paginationMeta($paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
        ];
    }
}
