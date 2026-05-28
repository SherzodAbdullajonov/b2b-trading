<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\DTOs\Order\OrderAllocationDTO;
use App\Services\Batch\BatchItemAvailabilityCalculator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class FifoBatchAllocator
{
    public function __construct(
        private readonly BatchItemAvailabilityCalculator $availability,
    ) {
    }

    /**
     * @param  array<int, int>  $needsByProductId
     * @return OrderAllocationDTO[]
     */
    public function allocate(array $needsByProductId): array
    {
        $productIds = array_keys($needsByProductId);
        sort($productIds);

        $allocations = [];
        $shortfalls  = [];

        foreach ($productIds as $productId) {
            $needed = $needsByProductId[$productId];
            if ($needed <= 0) {
                continue;
            }

            $rows = DB::table('batch_items as bi')
                ->join('batches as b', 'b.id', '=', 'bi.batch_id')
                ->where('bi.product_id', $productId)
                ->orderBy('bi.id')
                ->lockForUpdate()
                ->get(['bi.id', 'bi.batch_id', 'bi.sale_price', 'b.purchased_at'])
                ->sortBy([
                    ['purchased_at', 'asc'],
                    ['id', 'asc'],
                ])
                ->values();

            if ($rows->isEmpty()) {
                $shortfalls[$productId] = $needed;
                continue;
            }

            $batchItemIds = $rows->pluck('id')->map(fn ($id) => (int) $id)->all();
            $available    = $this->availability->availableForIds($batchItemIds, lockForUpdate: false);

            $remaining = $needed;
            foreach ($rows as $row) {
                if ($remaining === 0) {
                    break;
                }

                $avail = $available[(int) $row->id] ?? 0;
                if ($avail <= 0) {
                    continue;
                }

                $take = (int) min($remaining, $avail);

                $allocations[] = new OrderAllocationDTO(
                    productId:   $productId,
                    batchId:     (int) $row->batch_id,
                    batchItemId: (int) $row->id,
                    qty:         $take,
                    unitPrice:   (string) $row->sale_price,
                );

                $remaining -= $take;
            }

            if ($remaining > 0) {
                $shortfalls[$productId] = $remaining;
            }
        }

        if ($shortfalls !== []) {
            $errors = [];
            foreach ($shortfalls as $pid => $short) {
                $errors["products.{$pid}.qty"] =
                    "Insufficient stock for product {$pid}: short by {$short}.";
            }
            throw ValidationException::withMessages($errors);
        }

        return $allocations;
    }
}
