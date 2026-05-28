<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\DTOs\Order\OrderAllocationDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderAllocationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var OrderAllocationDTO $dto */
        $dto = $this->resource;

        return [
            'product_id'    => $dto->productId,
            'from_batch_id' => $dto->batchId,
            'batch_item_id' => $dto->batchItemId,
            'qty'           => $dto->qty,
            'unit_price'    => $dto->unitPrice,
        ];
    }
}
