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

        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            }
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
            'scheduled_at' => 'nullable|date',
            'is_published' => 'boolean',
        ]);

        $validated['organization_id'] = auth()->user()->organization_id;

        if ($request->is_published) {
            $validated['published_at'] = now();
        }

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
            'scheduled_at' => 'nullable|date',
            'is_published' => 'boolean',
        ]);

        if ($request->is_published && !$announcement->is_published) {
            $validated['published_at'] = now();
        }

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
            'status' => 'required|in:published,draft'
        ]);

        $orgId = auth()->user()->organization_id;

        $isPublished = $request->status === 'published';

        $announcements = Announcement::whereIn('id', $request->ids)
            ->where('organization_id', $orgId)
            ->get();

        foreach ($announcements as $announcement) {
            $announcement->update([
                'is_published' => $isPublished,
                'published_at' => $isPublished ? ($announcement->published_at ?? now()) : null
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => count($announcements) . ' announcement(s) updated successfully.'
        ]);
    }
}
