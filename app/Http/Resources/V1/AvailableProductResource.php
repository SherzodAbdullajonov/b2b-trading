<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\DTOs\Product\AvailableProductDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AvailableProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var AvailableProductDTO $dto */
        $dto = $this->resource;

        return [
            'id'            => $dto->id,
            'name'          => $dto->name,
            'category_name' => $dto->categoryName,
            'price'         => $dto->price,
            'qty'           => $dto->qty,
        ];
    }
}
