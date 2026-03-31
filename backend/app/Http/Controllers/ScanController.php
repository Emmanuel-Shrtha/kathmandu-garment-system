<?php

namespace App\Http\Controllers;

use App\Models\Bundle;
use App\Models\WorkerClaim;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScanController extends Controller
{
    public function claim(Request $request): JsonResponse
    {
        $data = $request->validate([
            'bundle_id'   => ['required', 'integer', 'exists:bundles,id'],
            'worker_id'   => ['required', 'integer', 'exists:workers,id'],
            'claimed_qty' => ['required', 'integer', 'min:1'],
            'client_uuid' => ['required', 'uuid'],
        ]);

        return DB::transaction(function () use ($data) {
            $clientUuid  = $data['client_uuid'];
            $overflowUuid = $clientUuid . '_overflow';

            /**
             * Idempotency:
             * If this request was already processed once, return the existing rows
             * instead of creating duplicates.
             */
            $existingClaims = WorkerClaim::query()
                ->whereIn('client_uuid', [$clientUuid, $overflowUuid])
                ->orderBy('id')
                ->get();

            if ($existingClaims->isNotEmpty()) {
                $bundle = Bundle::query()
                    ->whereKey($existingClaims->first()->bundle_id)
                    ->firstOrFail();

                return response()->json([
                    'success'        => true,
                    'duplicate'      => true,
                    'message'        => 'Claim already processed.',
                    'bundle_id'      => $bundle->id,
                    'bundle_qty'     => $bundle->bundle_qty,
                    'total_claimed'  => $this->getClaimedTotal($bundle->id),
                    'remaining_qty'   => $this->getRemainingQty($bundle->id, $bundle->bundle_qty),
                    'claims'         => $existingClaims,
                ]);
            }

            /**
             * Lock the bundle row so two supervisors cannot over-claim the same bundle
             * at the same time.
             */
            $bundle = Bundle::query()
                ->whereKey($data['bundle_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $alreadyClaimed = WorkerClaim::query()
                ->where('bundle_id', $bundle->id)
                ->whereNotIn('status', ['rejected', 'wasted'])
                ->sum('claimed_qty');

            $remainingQty = max($bundle->bundle_qty - $alreadyClaimed, 0);

            $requestedQty = (int) $data['claimed_qty'];
            $validQty     = min($requestedQty, $remainingQty);
            $overflowQty  = max($requestedQty - $validQty, 0);

            $createdClaims = [];

            /**
             * Valid portion:
             * This is the amount that still fits inside the bundle.
             */
            if ($validQty > 0) {
                $createdClaims[] = WorkerClaim::create([
                    'bundle_id'   => $bundle->id,
                    'worker_id'   => $data['worker_id'],
                    'client_uuid' => $clientUuid,
                    'claimed_qty' => $validQty,
                    'status'      => 'provisional',
                ]);
            }

            /**
             * Overflow portion:
             * If the claim goes beyond remaining quantity, we keep the overflow
             * as a separate conflicted row.
             */
            if ($overflowQty > 0) {
                $createdClaims[] = WorkerClaim::create([
                    'bundle_id'   => $bundle->id,
                    'worker_id'   => $data['worker_id'],
                    'client_uuid' => $validQty > 0 ? $overflowUuid : $clientUuid,
                    'claimed_qty' => $overflowQty,
                    'status'      => 'conflicted',
                ]);
            }

            /**
             * If the bundle was completely full and validQty is 0, the single row
             * above is already stored as conflicted using the original UUID.
             */

            $bundle->update([
                'current_holder_worker_id' => $data['worker_id'],
                'last_scanned_at'          => now(),
            ]);

            $totalClaimedNow = $this->getClaimedTotal($bundle->id);
            $remainingNow    = max($bundle->bundle_qty - $totalClaimedNow, 0);

            return response()->json([
                'success'       => true,
                'duplicate'     => false,
                'bundle_id'     => $bundle->id,
                'bundle_qty'    => $bundle->bundle_qty,
                'requested_qty' => $requestedQty,
                'valid_qty'     => $validQty,
                'overflow_qty'  => $overflowQty,
                'total_claimed' => $totalClaimedNow,
                'remaining_qty' => $remainingNow,
                'claims'        => $createdClaims,
            ], 201);
        });
    }

    private function getClaimedTotal(int $bundleId): int
    {
        return (int) WorkerClaim::query()
            ->where('bundle_id', $bundleId)
            ->whereNotIn('status', ['rejected', 'wasted'])
            ->sum('claimed_qty');
    }

    private function getRemainingQty(int $bundleId, int $bundleQty): int
    {
        $claimed = $this->getClaimedTotal($bundleId);

        return max($bundleQty - $claimed, 0);
    }
}