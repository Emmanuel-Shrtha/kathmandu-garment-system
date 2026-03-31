<?php

namespace App\Http\Controllers;

use App\Models\Bundle;
use App\Models\WorkerClaim;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function supervisor()
    {
        // 🔴 CONFLICTS COUNT
        $conflictsCount = WorkerClaim::where('status', 'conflicted')->count();

        // 📦 ACTIVE BUNDLES (recent activity)
        $activeBundles = Bundle::with('currentHolder')
            ->whereNotNull('last_scanned_at')
            ->orderByDesc('last_scanned_at')
            ->limit(10)
            ->get()
            ->map(function ($bundle) {
                return [
                    'bundle_id' => $bundle->id,
                    'bundle_qty' => $bundle->bundle_qty,
                    'holder' => $bundle->currentHolder?->full_name,
                    'last_seen' => $bundle->last_scanned_at,
                ];
            });

        // 🟢 TODAY'S EARNINGS (simple version)
        $today = now()->startOfDay();

        $earnings = WorkerClaim::where('status', 'provisional')
            ->where('created_at', '>=', $today)
            ->select('worker_id', DB::raw('SUM(claimed_qty) as total_pieces'))
            ->groupBy('worker_id')
            ->with('worker')
            ->get()
            ->map(function ($row) {
                return [
                    'worker_id' => $row->worker_id,
                    'worker_name' => $row->worker?->full_name,
                    'pieces' => $row->total_pieces,
                    'estimated_earnings' => $row->total_pieces * 10 // temp rate
                ];
            });

        // 📊 SUMMARY CARDS
        $totalWorkers = Worker::count();
        $activeWorkers = WorkerClaim::where('created_at', '>=', $today)
            ->distinct('worker_id')
            ->count('worker_id');

        return response()->json([
            'summary' => [
                'conflicts' => $conflictsCount,
                'active_workers' => $activeWorkers,
                'total_workers' => $totalWorkers,
            ],
            'active_bundles' => $activeBundles,
            'today_earnings' => $earnings
        ]);
    }
}