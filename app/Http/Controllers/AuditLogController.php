<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Read-only view of the audit trail (admin only). There is no edit or delete. */
class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $action = $request->query('action');

        $logs = AuditLog::with('actor')
            ->when($action, fn ($q) => $q->where('action', $action))
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        $actions = AuditLog::select('action')->distinct()->orderBy('action')->pluck('action');

        return view('audit-logs.index', compact('logs', 'actions', 'action'));
    }
}
