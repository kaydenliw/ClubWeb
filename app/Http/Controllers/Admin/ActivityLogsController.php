<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogsController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with(['user', 'organization'])
            ->orderBy('created_at', 'desc');

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by organization
        if ($request->filled('organization_id')) {
            $query->where('organization_id', $request->organization_id);
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
        $actions = ActivityLog::distinct()
            ->pluck('action');

        // Get all users for filter
        $users = User::orderBy('name')->get();

        // Get all organizations for filter
        $organizations = \App\Models\Organization::orderBy('name')->get();

        return view('admin.activity-logs.index', compact('logs', 'actions', 'users', 'organizations'));
    }
}
