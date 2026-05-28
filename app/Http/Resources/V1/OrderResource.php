<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\DTOs\Order\OrderResultDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var OrderResultDTO $dto */
        $dto = $this->resource;

        return [
            'order_id'    => $dto->orderId,
            'allocations' => OrderAllocationResource::collection($dto->allocations),
        ];
    }
}
