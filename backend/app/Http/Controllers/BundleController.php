<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bundle;

class BundleController extends Controller
{
    /**
     * Create multiple bundles for a specific order.
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_name' => 'required|string',
            'total_pieces' => 'required|integer',
            'qty_per_bundle' => 'required|integer',
        ]);

        $bundleCount = ceil($request->total_pieces / $request->qty_per_bundle);
        $createdBundles = [];

        for ($i = 1; $i <= $bundleCount; $i++) {
            // Logic to handle the last bundle if it has fewer pieces
            $currentQty = ($i == $bundleCount) 
                ? ($request->total_pieces - (($i - 1) * $request->qty_per_bundle)) 
                : $request->qty_per_bundle;

            $bundle = Bundle::create([
                'bundle_number' => $request->order_name . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'total_qty' => $currentQty,
                'status' => 'pending' // Ready to be scanned
            ]);

            $createdBundles[] = $bundle;
        }

        return response()->json([
            'message' => "Successfully created $bundleCount bundles.",
            'data' => $createdBundles
        ]);
    }

    public function index()
    {
        return Bundle::all();
    }
}