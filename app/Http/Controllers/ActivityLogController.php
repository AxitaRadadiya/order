<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ---------------------------------------------------------------
    // INDEX — paginated list with search + action filter
    // ---------------------------------------------------------------
    public function index(Request $request): View
    {
        $query = ActivityLog::with('user')->latest();

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by user name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('user_name',    'like', "%{$search}%")
                  ->orWhere('description','like', "%{$search}%")
                  ->orWhere('model_label','like', "%{$search}%");
            });
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(25)->withQueryString();

        $actions = ['login', 'logout', 'created', 'updated', 'deleted'];

        return view('admin.activity.index', compact('logs', 'actions'));
    }

    // ---------------------------------------------------------------
    // SHOW — detail view with old/new diff
    // ---------------------------------------------------------------
    public function show(ActivityLog $activityLog): View
    {
        return view('admin.activity.show', ['log' => $activityLog]);
    }
}