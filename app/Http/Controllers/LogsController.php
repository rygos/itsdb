<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\View\View;

class LogsController extends Controller
{
    public function index(): View
    {
        $logs = Log::query()
            // The dedicated overview mirrors the customer page and only lists customer-scoped logs.
            ->with(['user', 'customer'])
            ->where('section', 'customer')
            ->orderByDesc('created_at')
            ->get();

        return view('logs.index', [
            'logs' => $logs,
        ]);
    }
}
