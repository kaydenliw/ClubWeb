<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $query = Announcement::with('organization', 'creator');

        // Filter by approval status
        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }

        // Search filter
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        $announcements = $query->latest()->paginate(15)->withQueryString();

        // Get pending announcements count
        $pendingCount = Announcement::where('approval_status', 'pending_approval')->count();

        return view('admin.announcements.index', compact('announcements', 'pendingCount'));
    }

    public function show(Announcement $announcement)
    {
        $announcement->load('organization', 'creator');

        return view('admin.announcements.show', compact('announcement'));
    }

    public function approve(Announcement $announcement)
    {
        $announcement->update([
            'approval_status' => 'approved_pending_publish',
        ]);

        return response()->json(['success' => true]);
    }

    public function reject(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'reject_reason' => 'required|string|max:500',
        ]);

        $announcement->update([
            'approval_status' => 'rejected',
            'reject_reason' => $validated['reject_reason'],
        ]);

        return response()->json(['success' => true]);
    }

    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:announcements,id'
        ]);

        Announcement::whereIn('id', $validated['ids'])
            ->update(['approval_status' => 'approved_pending_publish', 'reject_reason' => null]);

        return response()->json(['success' => true, 'message' => count($validated['ids']) . ' announcement(s) approved']);
    }

    public function bulkReject(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:announcements,id',
            'reject_reason' => 'required|string|max:500'
        ]);

        Announcement::whereIn('id', $validated['ids'])
            ->update(['approval_status' => 'rejected', 'reject_reason' => $validated['reject_reason']]);

        return response()->json(['success' => true, 'message' => count($validated['ids']) . ' announcement(s) rejected']);
    }
}
