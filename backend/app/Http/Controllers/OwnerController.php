<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkerClaim;
use App\Models\WorkerDailySummary;
use App\Models\Bundle;
use Illuminate\Support\Facades\DB;

class OwnerController extends Controller
{
    public function dashboard()
    {
        // Immutable ledgers only visible if trustee_flag = true
        // Example: total profit this month
        $profit = DB::selectOne("
            SELECT 
                SUM(client_ledger.credit) as revenue,
                SUM(lot.unit_cost * wc.passed_qty) as material_cost,
                SUM(wc.passed_qty * variant_snapshot.piece_rate) as labor_cost,
                SUM(wc.wasted_qty * lot.unit_cost) as wastage
            FROM worker_claims wc
            JOIN bundles b ON wc.bundle_id = b.id
            JOIN variant_cost_snapshot variant_snapshot ON variant_snapshot.id = wc.variant_id
            JOIN lot_batch lot ON lot.id = b.lot_id
            JOIN client_ledger ON client_ledger.bundle_id = b.id
            WHERE client_ledger.date BETWEEN NOW() - INTERVAL 30 DAY AND NOW()
        ");

        return response()->json([
            'profit_summary' => $profit
        ]);
    }
}