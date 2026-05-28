<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\DTOs\BatchRefund\BatchRefundResultDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BatchRefundResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var BatchRefundResultDTO $dto */
        $dto = $this->resource;

        return [
            'batch_refund_id' => $dto->batchRefundId,
            'items_refunded'  => $dto->itemsRefunded,
        ];
    }
}
