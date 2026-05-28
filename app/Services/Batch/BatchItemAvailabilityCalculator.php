<?php

declare(strict_types=1);

namespace App\Services\Batch;

use App\Models\BatchItem;
use Illuminate\Support\Facades\DB;

final class BatchItemAvailabilityCalculator
{
    /**
     * @param  int[]  $batchItemIds
     * @return array<int, int>
     */
    public function availableForIds(array $batchItemIds, bool $lockForUpdate = false): array
    {
        if ($batchItemIds === []) {
            return [];
        }

        sort($batchItemIds);

        $query = BatchItem::query()->whereIn('id', $batchItemIds);
        if ($lockForUpdate) {
            $query = $query->lockForUpdate();
        }
        $items = $query->get(['id', 'qty']);

        $refunded = DB::table('batch_refund_items')
            ->whereIn('batch_item_id', $batchItemIds)
            ->groupBy('batch_item_id')
            ->select('batch_item_id', DB::raw('SUM(qty) AS total'))
            ->pluck('total', 'batch_item_id')
            ->all();

        $sold = DB::table('order_items')
            ->whereIn('batch_item_id', $batchItemIds)
            ->groupBy('batch_item_id')
            ->select('batch_item_id', DB::raw('SUM(qty) AS total'))
            ->pluck('total', 'batch_item_id')
            ->all();

        $returned = DB::table('order_refund_items as ori')
            ->join('order_items as oi', 'oi.id', '=', 'ori.order_item_id')
            ->whereIn('oi.batch_item_id', $batchItemIds)
            ->groupBy('oi.batch_item_id')
            ->select('oi.batch_item_id', DB::raw('SUM(ori.qty) AS total'))
            ->pluck('total', 'oi.batch_item_id')
            ->all();

        $available = [];
        foreach ($items as $item) {
            $id = (int) $item->id;
            $available[$id] = (int) $item->qty
                - (int) ($refunded[$id] ?? 0)
                - (int) ($sold[$id]     ?? 0)
                + (int) ($returned[$id] ?? 0);
        }

        return $available;
    }
}
