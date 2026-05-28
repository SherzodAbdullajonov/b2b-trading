<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\DTOs\Purchase\PurchaseResultDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Transforms a PurchaseResultDTO into the wire response for
 * POST /api/v1/purchases.
 *
 * @property-read PurchaseResultDTO $resource
 */
class BatchResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var PurchaseResultDTO $dto */
        $dto = $this->resource;

        return [
            'batch_id'      => $dto->batchId,
            'items_created' => $dto->itemsCreated,
        ];
    }
}
