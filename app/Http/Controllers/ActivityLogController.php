<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('log_type')) {
            $query->where('log_type', $request->log_type);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(20);

        $modules = ActivityLog::selectRaw('DISTINCT module')->pluck('module');
        $actions = ActivityLog::selectRaw('DISTINCT action')->pluck('action');
        $logTypes = ActivityLog::selectRaw('DISTINCT log_type')->pluck('log_type');

        return view('activity-logs.index', compact('logs', 'modules', 'actions', 'logTypes'));
    }

    public function show($id)
    {
        $log = ActivityLog::with('user')->findOrFail($id);

        return view('activity-logs.show', compact('log'));
    }
}
