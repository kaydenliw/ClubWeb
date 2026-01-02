<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogsController extends Controller
{
    public function index(Request $request)
    {
        $organization = auth()->user()->organization;

        $query = ActivityLog::where('organization_id', $organization->id)
            ->with('user')
            ->orderBy('created_at', 'desc');

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $logs = $query->paginate(50);

        // Get unique actions for filter
        $actions = ActivityLog::where('organization_id', $organization->id)
            ->distinct()
            ->pluck('action');

        // Get users for filter
        $users = $organization->users;

        return view('organization.activity-logs.index', compact('logs', 'actions', 'users'));
    }
}
