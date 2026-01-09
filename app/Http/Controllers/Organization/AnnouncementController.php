<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $query = Announcement::where('organization_id', auth()->user()->organization_id);

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }

        $announcements = $query->latest()->get();

        return view('organization.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('organization.announcements.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'publish_date' => 'nullable|date',
            'approval_status' => 'nullable|in:draft,pending_approval',
            'is_highlighted' => 'nullable|boolean',
        ]);

        // Check if trying to highlight and if limit is reached
        if ($request->has('is_highlighted') && $request->is_highlighted) {
            $highlightedCount = Announcement::where('organization_id', auth()->user()->organization_id)
                ->where('is_highlighted', true)
                ->count();

            if ($highlightedCount >= 5) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['is_highlighted' => 'Maximum of 5 announcements can be highlighted. Please remove highlight from another announcement first.']);
            }
        }

        $validated['organization_id'] = auth()->user()->organization_id;
        $validated['created_by'] = auth()->id();
        $validated['approval_status'] = $validated['approval_status'] ?? 'draft';
        $validated['is_highlighted'] = $request->has('is_highlighted') ? true : false;

        Announcement::create($validated);

        return redirect()->route('organization.announcements.index')
            ->with('success', 'Announcement created successfully.');
    }

    public function show(Announcement $announcement)
    {
        if ($announcement->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }

        return view('organization.announcements.show', compact('announcement'));
    }

    public function edit(Announcement $announcement)
    {
        if ($announcement->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }

        return view('organization.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        if ($announcement->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'publish_date' => 'nullable|date',
            'approval_status' => 'nullable|in:draft,pending_approval',
            'is_highlighted' => 'nullable|boolean',
        ]);

        // Check if trying to highlight and if limit is reached
        if ($request->has('is_highlighted') && $request->is_highlighted && !$announcement->is_highlighted) {
            $highlightedCount = Announcement::where('organization_id', auth()->user()->organization_id)
                ->where('is_highlighted', true)
                ->where('id', '!=', $announcement->id)
                ->count();

            if ($highlightedCount >= 5) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['is_highlighted' => 'Maximum of 5 announcements can be highlighted. Please remove highlight from another announcement first.']);
            }
        }

        $validated['is_highlighted'] = $request->has('is_highlighted') ? true : false;

        $announcement->update($validated);

        return redirect()->route('organization.announcements.index')
            ->with('success', 'Announcement updated successfully.');
    }

    public function destroy(Announcement $announcement)
    {
        if ($announcement->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }

        $announcement->delete();

        return redirect()->route('organization.announcements.index')
            ->with('success', 'Announcement deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:announcements,id'
        ]);

        $orgId = auth()->user()->organization_id;

        $deleted = Announcement::whereIn('id', $request->ids)
            ->where('organization_id', $orgId)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => $deleted . ' announcement(s) deleted successfully.'
        ]);
    }

    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:announcements,id',
            'approval_status' => 'required|in:draft,pending_approval'
        ]);

        $orgId = auth()->user()->organization_id;

        $updated = Announcement::whereIn('id', $request->ids)
            ->where('organization_id', $orgId)
            ->update(['approval_status' => $request->approval_status]);

        return response()->json([
            'success' => true,
            'message' => $updated . ' announcement(s) updated successfully.'
        ]);
    }

    public function submitForApproval(Announcement $announcement)
    {
        if ($announcement->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }

        $announcement->update(['approval_status' => 'pending_approval']);

        return redirect()->route('organization.announcements.show', $announcement)
            ->with('success', 'Announcement submitted for approval.');
    }

    public function toggleHighlight(Request $request)
    {
        $request->validate([
            'announcement_id' => 'required|exists:announcements,id',
            'is_highlighted' => 'required|boolean',
        ]);

        $announcement = Announcement::findOrFail($request->announcement_id);

        if ($announcement->organization_id !== auth()->user()->organization_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        // Check if trying to highlight and if limit is reached
        if ($request->is_highlighted) {
            $highlightedCount = Announcement::where('organization_id', auth()->user()->organization_id)
                ->where('is_highlighted', true)
                ->where('id', '!=', $announcement->id)
                ->count();

            if ($highlightedCount >= 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maximum of 5 announcements can be highlighted. Please remove highlight from another announcement first.'
                ], 422);
            }
        }

        $announcement->update(['is_highlighted' => $request->is_highlighted]);

        return response()->json([
            'success' => true,
            'message' => $request->is_highlighted ? 'Announcement highlighted successfully.' : 'Highlight removed successfully.'
        ]);
    }
}
