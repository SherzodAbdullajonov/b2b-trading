<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTOs\Purchase\PurchaseInputDTO;
use App\Models\Batch;
use App\Models\BatchItem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class BatchRepository
{
    /**
     * Create the batch header
     */
    public function createWithItems(PurchaseInputDTO $input): Batch
    {
        $batch = Batch::query()->create([
            'provider_id'  => $input->providerId,
            'storage_id'   => $input->storageId,
            'purchased_at' => $input->purchasedAt,
            'note'         => $input->note,
        ]);

        // Bulk insert all items — one INSERT, not N.
        $now = Carbon::now();
        $rows = array_map(
            fn ($line) => [
                'batch_id'   => $batch->id,
                'product_id' => $line->productId,
                'qty'        => $line->qty,
                'unit_cost'  => $line->unitCost,
                'sale_price' => $line->salePrice,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            $input->lines,
        );

        BatchItem::query()->insert($rows);

        return $batch;
    }

    public function profitPerBatch(): Collection
    {
        $costPerBatch = DB::table('batch_items as bi')
            ->leftJoinSub(
                DB::table('batch_refund_items')
                    ->select('batch_item_id', DB::raw('SUM(qty) AS refunded_qty'))
                    ->groupBy('batch_item_id'),
                'br',
                'br.batch_item_id', '=', 'bi.id'
            )
            ->select(
                'bi.batch_id',
                DB::raw('SUM(bi.qty * bi.unit_cost - COALESCE(br.refunded_qty, 0) * bi.unit_cost) AS cost')
            )
            ->groupBy('bi.batch_id');

        $revenuePerBatch = DB::table('order_items as oi')
            ->join('batch_items as bi', 'bi.id', '=', 'oi.batch_item_id')
            ->leftJoinSub(
                DB::table('order_refund_items')
                    ->select('order_item_id', DB::raw('SUM(qty) AS returned_qty'))
                    ->groupBy('order_item_id'),
                'orf',
                'orf.order_item_id', '=', 'oi.id'
            )
            ->select(
                'bi.batch_id',
                DB::raw('SUM(oi.qty * oi.unit_price - COALESCE(orf.returned_qty, 0) * oi.unit_price) AS revenue')
            )
            ->groupBy('bi.batch_id');

        return DB::table('batches as b')
            ->leftJoinSub($costPerBatch,    'c', 'c.batch_id', '=', 'b.id')
            ->leftJoinSub($revenuePerBatch, 'r', 'r.batch_id', '=', 'b.id')
            ->orderBy('b.id')
            ->select([
                'b.id AS batch_id',
                'b.provider_id',
                'b.purchased_at',
                DB::raw('COALESCE(c.cost, 0) AS cost'),
                DB::raw('COALESCE(r.revenue, 0) AS revenue'),
                DB::raw('COALESCE(r.revenue, 0) - COALESCE(c.cost, 0) AS profit'),
            ])
            ->get();
    }
}
