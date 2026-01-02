<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\FAQ;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $query = FAQ::where('organization_id', auth()->user()->organization_id);

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('question', 'like', '%' . $request->search . '%')
                  ->orWhere('answer', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $faqs = $query->orderBy('order')->get();

        return view('organization.faqs.index', compact('faqs'));
    }

    public function create()
    {
        return view('organization.faqs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'order' => 'nullable|integer|min:0',
        ]);

        $validated['organization_id'] = auth()->user()->organization_id;
        $validated['order'] = $validated['order'] ?? 0;

        FAQ::create($validated);

        return redirect()->route('organization.faqs.index')
            ->with('success', 'FAQ created successfully.');
    }

    public function edit(FAQ $faq)
    {
        if ($faq->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }

        return view('organization.faqs.edit', compact('faq'));
    }

    public function update(Request $request, FAQ $faq)
    {
        if ($faq->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }

        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'order' => 'nullable|integer|min:0',
        ]);

        $validated['order'] = $validated['order'] ?? 0;

        $faq->update($validated);

        return redirect()->route('organization.faqs.index')
            ->with('success', 'FAQ updated successfully.');
    }

    public function destroy(FAQ $faq)
    {
        if ($faq->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }

        $faq->delete();

        return redirect()->route('organization.faqs.index')
            ->with('success', 'FAQ deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:faqs,id'
        ]);

        $orgId = auth()->user()->organization_id;

        $deleted = FAQ::whereIn('id', $request->ids)
            ->where('organization_id', $orgId)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => $deleted . ' FAQ(s) deleted successfully.'
        ]);
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*.id' => 'required|exists:faqs,id',
            'order.*.order' => 'required|integer|min:1'
        ]);

        $orgId = auth()->user()->organization_id;

        foreach ($request->order as $item) {
            FAQ::where('id', $item['id'])
                ->where('organization_id', $orgId)
                ->update(['order' => $item['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'FAQ order updated successfully.'
        ]);
    }
}
