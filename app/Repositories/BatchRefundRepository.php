<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTOs\BatchRefund\BatchRefundInputDTO;
use App\Models\BatchRefund;
use App\Models\BatchRefundItem;
use Illuminate\Support\Carbon;

final class BatchRefundRepository
{
    public function createWithItems(BatchRefundInputDTO $input): BatchRefund
    {
        $refund = BatchRefund::query()->create([
            'batch_id'    => $input->batchId,
            'refunded_at' => $input->refundedAt,
            'reason'      => $input->reason,
        ]);

        $now = Carbon::now();
        $rows = array_map(
            fn ($line) => [
                'batch_refund_id' => $refund->id,
                'batch_item_id'   => $line->batchItemId,
                'qty'             => $line->qty,
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            $input->lines,
        );

        BatchRefundItem::query()->insert($rows);

        return $refund;
    }
}
