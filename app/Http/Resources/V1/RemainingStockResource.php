<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\DTOs\Storage\RemainingStockDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RemainingStockResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var RemainingStockDTO $dto */
        $dto = $this->resource;

        return [
            'storage_id'   => $dto->storageId,
            'storage_name' => $dto->storageName,
            'product_id'   => $dto->productId,
            'product_name' => $dto->productName,
            'qty'          => $dto->qty,
        ];
    }
}
