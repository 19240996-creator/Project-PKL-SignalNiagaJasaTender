<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Technician;
use App\Models\TechnicianAvailability;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TechnicianController extends Controller
{
    public function index(): View
    {
        $technicians = Technician::with(['user', 'availabilities', 'assignments.job', 'deletionRequester'])
            ->withCount(['assignments as active_assignments' => fn ($query) => $query
                ->whereIn('status', ['assigned', 'in_progress'])
                ->whereHas('job', fn ($q) => $q->where('status', '!=', 'Selesai')->where('progress', '<', 100))
            ])
            ->orderBy('name')
            ->get();

        $pendingDeletionCount = $technicians->where('deletion_status', 'pending_deletion')->count();

        return view('services.technicians.index', compact('technicians', 'pendingDeletionCount'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(!Auth::user()?->isTechnician(), 403);
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id|unique:technicians,user_id',
            'name' => 'required|string|max:150',
            'phone' => 'nullable|string|max:40',
            'email' => 'nullable|email|max:150',
            'competencies' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        Technician::create($validated);
        return back()->with('success', 'Data teknisi berhasil ditambahkan.');
    }

    public function update(Request $request, Technician $technician): RedirectResponse
    {
        abort_unless(!Auth::user()?->isTechnician(), 403);
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id|unique:technicians,user_id,' . $technician->id,
            'name' => 'required|string|max:150',
            'phone' => 'nullable|string|max:40',
            'email' => 'nullable|email|max:150',
            'competencies' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        $technician->update($validated);
        return back()->with('success', 'Data teknisi berhasil diperbarui.');
    }

    public function destroy(Request $request, Technician $technician): RedirectResponse
    {
        $user = Auth::user();

        // Jika Admin, alihkan otomatis ke permohonan izin hapus
        if ($user && $user->isAdmin()) {
            return $this->requestDeletion($request, $technician);
        }

        // Hanya Manager atau Owner yang berhak menghapus langsung
        if (!$user || (!$user->isManager() && !$user->isOwner())) {
            abort(403, 'Hanya Manajemen (Manager atau Owner) yang berhak menghapus data teknisi.');
        }

        $activeCount = $technician->assignments()
            ->whereIn('status', ['assigned', 'in_progress'])
            ->whereHas('job', fn ($q) => $q->where('status', '!=', 'Selesai')->where('progress', '<', 100))
            ->count();

        if ($activeCount > 0) {
            return back()->with('error', "Tidak dapat menghapus teknisi \"{$technician->name}\" karena masih memiliki {$activeCount} tugas aktif. Selesaikan atau alihkan penugasan terlebih dahulu.");
        }

        $name = $technician->name;

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'delete',
            'table_name' => 'technicians',
            'record_id' => $technician->id,
            'old_values' => [
                'name' => $technician->name,
                'phone' => $technician->phone,
                'email' => $technician->email,
                'status' => $technician->status,
            ],
            'new_values' => ['status' => 'deleted_by_manager'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        $technician->delete();

        return back()->with('success', "Data teknisi \"{$name}\" berhasil dihapus oleh Manajemen.");
    }

    public function requestDeletion(Request $request, Technician $technician): RedirectResponse
    {
        $user = Auth::user();

        if (!$user || (!in_array($user->role?->name, ['admin', 'manager', 'owner'], true) && !$user->isAdmin())) {
            abort(403, 'Anda tidak memiliki hak untuk mengajukan penghapusan data teknisi.');
        }

        if ($technician->deletion_status === 'pending_deletion') {
            return back()->with('error', "Permohonan penghapusan teknisi \"{$technician->name}\" sudah diajukan dan sedang menunggu persetujuan Manager.");
        }

        $activeCount = $technician->assignments()
            ->whereIn('status', ['assigned', 'in_progress'])
            ->whereHas('job', fn ($q) => $q->where('status', '!=', 'Selesai')->where('progress', '<', 100))
            ->count();

        if ($activeCount > 0) {
            return back()->with('error', "Tidak dapat mengajukan penghapusan teknisi \"{$technician->name}\" karena masih memiliki {$activeCount} tugas aktif. Selesaikan atau alihkan penugasan terlebih dahulu.");
        }

        $validated = $request->validate([
            'deletion_reason' => 'required|string|min:5|max:1000',
        ], [
            'deletion_reason.required' => 'Catatan alasan penghapusan teknisi wajib diisi.',
            'deletion_reason.min' => 'Catatan alasan penghapusan minimal 5 karakter.',
            'deletion_reason.max' => 'Catatan alasan penghapusan maksimal 1000 karakter.',
        ]);

        $technician->update([
            'deletion_status' => 'pending_deletion',
            'deletion_reason' => $validated['deletion_reason'],
            'deletion_requested_by' => $user->id,
            'deletion_requested_at' => now(),
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'request_deletion',
            'table_name' => 'technicians.request_deletion',
            'record_id' => $technician->id,
            'old_values' => [
                'name' => $technician->name,
                'phone' => $technician->phone,
            ],
            'new_values' => [
                'deletion_status' => 'pending_deletion',
                'deletion_reason' => $validated['deletion_reason'],
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return back()->with('success', "Permohonan penghapusan teknisi \"{$technician->name}\" berhasil diajukan kepada Manager. Menunggu persetujuan.");
    }

    public function approveDeletion(Request $request, Technician $technician): RedirectResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->isManager() && !$user->isOwner())) {
            return back()->with('error', 'Hanya Manajemen (Manager atau Owner) yang berhak menyetujui penghapusan data teknisi.');
        }

        if ($technician->deletion_status !== 'pending_deletion') {
            return back()->with('error', 'Teknisi ini tidak dalam status menunggu persetujuan hapus.');
        }

        $activeCount = $technician->assignments()
            ->whereIn('status', ['assigned', 'in_progress'])
            ->whereHas('job', fn ($q) => $q->where('status', '!=', 'Selesai')->where('progress', '<', 100))
            ->count();

        if ($activeCount > 0) {
            return back()->with('error', "Persetujuan gagal. Teknisi \"{$technician->name}\" masih memiliki {$activeCount} tugas aktif yang belum selesai.");
        }

        $techName = $technician->name;
        $requesterName = $technician->deletionRequester?->name ?? 'Admin';
        $reason = $technician->deletion_reason;

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'approve_deletion',
            'table_name' => 'technicians.approve_deletion',
            'record_id' => $technician->id,
            'old_values' => [
                'name' => $techName,
                'phone' => $technician->phone,
                'email' => $technician->email,
                'requested_by' => $requesterName,
                'deletion_reason' => $reason,
            ],
            'new_values' => [
                'status' => 'deleted_by_manager_approval',
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        $technician->delete();

        return back()->with('success', "Izin penghapusan teknisi \"{$techName}\" disetujui. Data teknisi telah dihapus.");
    }

    public function rejectDeletion(Request $request, Technician $technician): RedirectResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->isManager() && !$user->isOwner())) {
            return back()->with('error', 'Hanya Manajemen (Manager atau Owner) yang berhak menolak permohonan hapus teknisi.');
        }

        if ($technician->deletion_status !== 'pending_deletion') {
            return back()->with('error', 'Teknisi ini tidak dalam status menunggu persetujuan hapus.');
        }

        $techName = $technician->name;
        $oldReason = $technician->deletion_reason;
        $rejectNotes = $request->input('notes', "Permohonan hapus ditolak oleh {$user->name}");

        $technician->update([
            'deletion_status' => 'none',
            'deletion_reason' => null,
            'deletion_requested_by' => null,
            'deletion_requested_at' => null,
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'reject_deletion',
            'table_name' => 'technicians.reject_deletion',
            'record_id' => $technician->id,
            'old_values' => [
                'name' => $techName,
                'deletion_status' => 'pending_deletion',
                'reason' => $oldReason,
            ],
            'new_values' => [
                'deletion_status' => 'none',
                'notes' => $rejectNotes,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return back()->with('success', "Permohonan penghapusan teknisi \"{$techName}\" telah ditolak. Data teknisi tetap aktif.");
    }

    public function storeAvailability(Request $request, Technician $technician): RedirectResponse
    {
        abort_unless(!Auth::user()?->isTechnician() || $technician->user_id === Auth::id(), 403);
        $validated = $request->validate([
            'available_from' => 'required|date',
            'available_until' => 'required|date|after_or_equal:available_from',
            'status' => 'required|in:available,leave,unavailable',
            'notes' => 'nullable|string',
        ]);

        $technician->availabilities()->create($validated);
        return back()->with('success', 'Ketersediaan teknisi berhasil dicatat.');
    }
}

