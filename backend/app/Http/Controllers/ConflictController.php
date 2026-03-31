<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkerClaim;

class ConflictController extends Controller
{
    /**
     * Display a listing of conflicted/provisional claims.
     */
    public function index()
    {
        // This is the method Laravel couldn't find
        return WorkerClaim::whereIn('status', ['conflicted', 'provisional'])->get();
    }

    /**
     * Resolve the conflict based on supervisor action.
     */
    public function resolve(Request $request, $id)
    {
        $claim = WorkerClaim::findOrFail($id);
        $action = $request->action;

        switch ($action) {
            case 'reduce_claim':
                $claim->update([
                    'claimed_qty' => $request->new_qty,
                    'status' => 'passed'
                ]);
                break;

            case 'mark_waste':
                $claim->update([
                    'status' => 'wasted',
                    'wasted_qty' => $claim->claimed_qty
                ]);
                break;

            case 'override':
                $claim->update(['status' => 'passed']);
                break;
                
            default:
                return response()->json(['error' => 'Invalid action'], 400);
        }

        return response()->json([
            'message' => 'Conflict resolved as ' . $action, 
            'data' => $claim
        ]);
    }
}