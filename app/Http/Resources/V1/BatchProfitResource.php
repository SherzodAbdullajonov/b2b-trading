<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\DTOs\Batch\BatchProfitDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BatchProfitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var BatchProfitDTO $dto */
        $dto = $this->resource;

        return [
            'batch_id'     => $dto->batchId,
            'provider_id'  => $dto->providerId,
            'purchased_at' => $dto->purchasedAt,
            'cost'         => $dto->cost,
            'revenue'      => $dto->revenue,
            'profit'       => $dto->profit,
        ];
    }
}
