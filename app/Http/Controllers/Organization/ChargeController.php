<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Charge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ChargeController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $orgId = auth()->user()->organization_id;

        $query = Charge::where('organization_id', $orgId);

        // Search filter
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $charges = $query->withCount('members')->latest()->paginate(10)->withQueryString();

        return view('organization.charges.index', compact('charges'));
    }

    public function create()
    {
        return view('organization.charges.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'is_recurring' => 'required|boolean',
            'recurring_months' => 'nullable|integer|min:1|max:12',
            'status' => 'nullable|in:active,inactive',
            'scheduled_at' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'approval_status' => 'nullable|in:draft,pending',
        ]);

        $validated['organization_id'] = auth()->user()->organization_id;
        $validated['status'] = $validated['status'] ?? 'active';
        $validated['approval_status'] = $validated['approval_status'] ?? 'draft';

        // Set recurring_frequency
        if ($validated['is_recurring']) {
            $validated['recurring_frequency'] = 'monthly';
        } else {
            $validated['recurring_frequency'] = 'one-time';
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('charges', 'public');
        }

        Charge::create($validated);

        return redirect()->route('organization.charges.index')
            ->with('success', 'Charge created successfully.');
    }

    public function show(Charge $charge)
    {
        if ($charge->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }

        $members = $charge->members()->paginate(10);

        return view('organization.charges.show', compact('charge', 'members'));
    }

    public function edit(Charge $charge)
    {
        if ($charge->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }

        return view('organization.charges.edit', compact('charge'));
    }

    public function update(Request $request, Charge $charge)
    {
        if ($charge->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'is_recurring' => 'required|boolean',
            'recurring_months' => 'nullable|integer|min:1|max:12',
            'status' => 'required|in:active,inactive',
            'scheduled_at' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'approval_status' => 'nullable|in:draft,pending',
        ]);

        // Set recurring_frequency based on is_recurring and recurring_months
        if ($validated['is_recurring']) {
            $validated['recurring_frequency'] = 'monthly'; // Will be calculated based on months
        } else {
            $validated['recurring_frequency'] = 'one-time';
        }

        // Handle image removal
        if ($request->input('remove_image') == '1') {
            if ($charge->image) {
                Storage::disk('public')->delete($charge->image);
            }
            $validated['image'] = null;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($charge->image) {
                Storage::disk('public')->delete($charge->image);
            }
            $validated['image'] = $request->file('image')->store('charges', 'public');
        }

        $charge->update($validated);

        return redirect()->route('organization.charges.index')
            ->with('success', 'Charge updated successfully.');
    }

    public function destroy(Charge $charge)
    {
        if ($charge->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }

        // Delete image if exists
        if ($charge->image) {
            Storage::disk('public')->delete($charge->image);
        }

        $charge->delete();

        return redirect()->route('organization.charges.index')
            ->with('success', 'Charge deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:charges,id'
        ]);

        $orgId = auth()->user()->organization_id;

        $charges = Charge::whereIn('id', $request->ids)
            ->where('organization_id', $orgId)
            ->get();

        foreach ($charges as $charge) {
            // Delete image if exists
            if ($charge->image) {
                Storage::disk('public')->delete($charge->image);
            }
            $charge->delete();
        }

        return response()->json([
            'success' => true,
            'message' => count($charges) . ' charge(s) deleted successfully.'
        ]);
    }

    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:charges,id',
            'status' => 'required|in:active,inactive'
        ]);

        $orgId = auth()->user()->organization_id;

        $updated = Charge::whereIn('id', $request->ids)
            ->where('organization_id', $orgId)
            ->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => $updated . ' charge(s) updated successfully.'
        ]);
    }

    public function submitForApproval(Charge $charge)
    {
        if ($charge->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }

        $charge->update([
            'approval_status' => 'pending',
            'reject_reason' => null
        ]);

        return redirect()->route('organization.charges.show', $charge)
            ->with('success', 'Charge submitted for approval.');
    }
}
