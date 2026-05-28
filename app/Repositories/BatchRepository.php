<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTOs\Purchase\PurchaseInputDTO;
use App\Models\Batch;
use App\Models\BatchItem;
use Illuminate\Support\Carbon;

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
}
